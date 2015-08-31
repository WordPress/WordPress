<?php
/*

 $Id: sitemap-builder.php 1026247 2014-11-15 16:47:36Z arnee $

*/
/**
 * Default sitemap builder
 *
 * @author Arne Brachhold
 * @package sitemap
 * @since 4.0
 */
class GoogleSitemapGeneratorStandardBuilder {

	/**
	 * Creates a new GoogleSitemapGeneratorStandardBuilder instance
	 */
	public function __construct() {
		add_action("sm_build_index", array($this, "Index"), 10, 1);
		add_action("sm_build_content", array($this, "Content"), 10, 3);

		add_filter("sm_sitemap_for_post", array($this, "GetSitemapUrlForPost"), 10, 3);
	}

	/**
	 * Generates the content of the requested sitemap
	 *
	 * @param $gsg GoogleSitemapGenerator
	 * @param $type String The type of the sitemap
	 * @param $params String Parameters for the sitemap
	 */
	public function Content($gsg, $type, $params) {

		switch($type) {
			case "pt":
				$this->BuildPosts($gsg, $params);
				break;
			case "archives":
				$this->BuildArchives($gsg);
				break;
			case "authors":
				$this->BuildAuthors($gsg);
				break;
			case "tax":
				$this->BuildTaxonomies($gsg, $params);
				break;
			case "externals":
				$this->BuildExternals($gsg);
				break;
			case "misc":
				$this->BuildMisc($gsg);
				break;
		}
	}

	/**
	 * Generates the content for the post sitemap
	 *
	 * @param $gsg GoogleSitemapGenerator
	 * @param $params string
	 */
	public function BuildPosts($gsg, $params) {

		if(!$pts = strrpos($params, "-")) return;

		$pts = strrpos($params, "-", $pts - strlen($params) - 1);

		$postType = substr($params, 0, $pts);

		if(!$postType || !in_array($postType, $gsg->GetActivePostTypes())) return;

		$params = substr($params, $pts + 1);

		/**@var $wpdb wpdb */
		global $wpdb;

		if(preg_match('/^([0-9]{4})\-([0-9]{2})$/', $params, $matches)) {
			$year = $matches[1];
			$month = $matches[2];

			//Excluded posts by ID
			$excludedPostIDs = $gsg->GetExcludedPostIDs($gsg);
			$exPostSQL = "";
			if(count($excludedPostIDs) > 0) {
				$exPostSQL = "AND p.ID NOT IN (" . implode(",", $excludedPostIDs) . ")";
			}

			//Excluded categories by taxonomy ID
			$excludedCategoryIDs = $gsg->GetExcludedCategoryIDs($gsg);
			$exCatSQL = "";
			if(count($excludedCategoryIDs) > 0) {
				$exCatSQL = "AND ( p.ID NOT IN ( SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE term_id IN ( " . implode(",", $excludedCategoryIDs) . "))))";
			}

			//Statement to query the actual posts for this post type
			$qs = "
				SELECT
					p.ID,
					p.post_author,
					p.post_status,
					p.post_name,
					p.post_parent,
					p.post_type,
					p.post_date,
					p.post_date_gmt,
					p.post_modified,
					p.post_modified_gmt,
					p.comment_count
				FROM
					{$wpdb->posts} p
				WHERE
					p.post_password = ''
					AND p.post_type = '%s'
					AND p.post_status = 'publish'
					AND YEAR(p.post_date_gmt) = %d
					AND MONTH(p.post_date_gmt) = %d
					{$exPostSQL}
					{$exCatSQL}
				ORDER BY
					p.post_date_gmt DESC
			";

			//Query for counting all relevant posts for this post type
			$qsc = "
				SELECT
					COUNT(*)
				FROM
					{$wpdb->posts} p
				WHERE
					p.post_password = ''
					AND p.post_type = '%s'
					AND p.post_status = 'publish'
					{$exPostSQL}
					{$exCatSQL}
			";

			$q = $wpdb->prepare($qs, $postType, $year, $month);

			$posts = $wpdb->get_results($q);

			if(($postCount = count($posts)) > 0) {
				/** @var $priorityProvider GoogleSitemapGeneratorPrioProviderBase */
				$priorityProvider = NULL;

				if($gsg->GetOption("b_prio_provider") != '') {

					//Number of comments for all posts
					$cacheKey = __CLASS__ . "::commentCount";
					$commentCount = wp_cache_get($cacheKey,'sitemap');
					if ($commentCount == false) {
						$commentCount = $wpdb->get_var("SELECT COUNT(*) as `comment_count` FROM {$wpdb->comments} WHERE `comment_approved`='1'");
						wp_cache_set($cacheKey, $commentCount, 'sitemap', 20);
					}

					//Number of all posts matching our criteria
					$cacheKey = __CLASS__  . "::totalPostCount::$postType";
					$totalPostCount = wp_cache_get($cacheKey,'sitemap');
					if($totalPostCount === false) {
						$totalPostCount = $wpdb->get_var($wpdb->prepare($qsc,$postType));
						wp_cache_add($cacheKey,$totalPostCount, 'sitemap', 20);
					}

					//Initialize a new priority provider
					$providerClass = $gsg->GetOption('b_prio_provider');
					$priorityProvider = new $providerClass($commentCount, $totalPostCount);
				}

				//Default priorities
				$defaultPriorityForPosts = $gsg->GetOption('pr_posts');
				$defaultPriorityForPages = $gsg->GetOption('pr_pages');

				//Minimum priority
				$minimumPriority = $gsg->GetOption('pr_posts_min');

				//Change frequencies
				$changeFrequencyForPosts = $gsg->GetOption('cf_posts');
				$changeFrequencyForPages = $gsg->GetOption('cf_pages');

				//Page as home handling
				$homePid = 0;
				$home = get_home_url();
				if('page' == get_option('show_on_front') && get_option('page_on_front')) {
					$pageOnFront = get_option('page_on_front');
					$p = get_post($pageOnFront);
					if($p) $homePid = $p->ID;
				}

				foreach($posts AS $post) {

					//Fill the cache with our DB result. Since it's incomplete (no text-content for example), we will clean it later.
					//This is required since the permalink function will do a query for every post otherwise.
					//wp_cache_add($post->ID, $post, 'posts');

					//Full URL to the post
					$permalink = get_permalink($post);

					//Exclude the home page and placeholder items by some plugins. Also include only internal links.
					if(
						!empty($permalink)
						&& $permalink != $home
						&& $post->ID != $homePid
						&& strpos( $permalink, $home) !== false
					) {

						//Default Priority if auto calc is disabled
						$priority = ($postType == 'page' ? $defaultPriorityForPages : $defaultPriorityForPosts);

						//If priority calc. is enabled, calculate (but only for posts, not pages)!
						if($priorityProvider !== null && $postType == 'post') {
							$priority = $priorityProvider->GetPostPriority($post->ID, $post->comment_count, $post);
						}

						//Ensure the minimum priority
						if($postType == 'post' && $minimumPriority > 0 && $priority < $minimumPriority) $priority = $minimumPriority;

						//ADdd the URL to the sitemap
						$gsg->AddUrl(
							$permalink,
							$gsg->GetTimestampFromMySql($post->post_modified_gmt && $post->post_modified_gmt != '0000-00-00 00:00:00'? $post->post_modified_gmt : $post->post_date_gmt),
							($postType == 'page' ? $changeFrequencyForPages : $changeFrequencyForPosts),
							$priority, $post->ID);
					}

					//Why not use clean_post_cache? Because some plugin will go crazy then (lots of database queries)
					//The post cache was not populated in a clean way, so we also won't delete it using the API.
					//wp_cache_delete( $post->ID, 'posts' );
					unset($post);
				}
			}

			unset($posts);
		}
	}

	/**
	 * Generates the content for the archives sitemap
	 *
	 * @param $gsg GoogleSitemapGenerator
	 */
	public function BuildArchives($gsg) {
		/** @var $wpdb wpdb */
		global $wpdb;
		$now = current_time('mysql', true);

		$archives = $wpdb->get_results("
			SELECT DISTINCT
				YEAR(post_date_gmt) AS `year`,
				MONTH(post_date_gmt) AS `month`,
				MAX(post_date_gmt) AS last_mod,
				count(ID) AS posts
			FROM
				$wpdb->posts
			WHERE
				post_date_gmt < '$now'
				AND post_status = 'publish'
				AND post_type = 'post'
			GROUP BY
				YEAR(post_date_gmt),
				MONTH(post_date_gmt)
			ORDER BY
				post_date_gmt DESC
		");

		if($archives) {
			foreach($archives as $archive) {

				$url = get_month_link($archive->year, $archive->month);

				//Archive is the current one
				if($archive->month == date("n") && $archive->year == date("Y")) {
					$changeFreq = $gsg->GetOption("cf_arch_curr");
				} else { // Archive is older
					$changeFreq = $gsg->GetOption("cf_arch_old");
				}

				$gsg->AddUrl($url, $gsg->GetTimestampFromMySql($archive->last_mod), $changeFreq, $gsg->GetOption("pr_arch"));
			}
		}
	}

	/**
	 * Generates the misc sitemap
	 *
	 * @param $gsg GoogleSitemapGenerator
	 */
	public function BuildMisc($gsg) {

		$lm = get_lastpostmodified('gmt');

		if($gsg->GetOption("in_home")) {
			$home = get_bloginfo('url');

			//Add the home page (WITH a slash!)
			if($gsg->GetOption("in_home")) {
				if('page' == get_option('show_on_front') && get_option('page_on_front')) {
					$pageOnFront = get_option('page_on_front');
					$p = get_post($pageOnFront);
					if($p) {
						$gsg->AddUrl(trailingslashit($home), $gsg->GetTimestampFromMySql(($p->post_modified_gmt && $p->post_modified_gmt != '0000-00-00 00:00:00'
								? $p->post_modified_gmt
								: $p->post_date_gmt)), $gsg->GetOption("cf_home"), $gsg->GetOption("pr_home"));
					}
				} else {
					$gsg->AddUrl(trailingslashit($home), ($lm ? $gsg->GetTimestampFromMySql($lm)
							: time()), $gsg->GetOption("cf_home"), $gsg->GetOption("pr_home"));
				}
			}
		}

		if($gsg->IsXslEnabled() && $gsg->GetOption("b_html") === true) {
			$gsg->AddUrl($gsg->GetXmlUrl("", "", array("html" => true)), ($lm ? $gsg->GetTimestampFromMySql($lm)
					: time()));
		}

		do_action('sm_buildmap');
	}

	/**
	 * Generates the author sitemap
	 *
	 * @param $gsg GoogleSitemapGenerator
	 */
	public function BuildAuthors($gsg) {
		/** @var $wpdb wpdb */
		global $wpdb;

		//Unfortunately there is no API function to get all authors, so we have to do it the dirty way...
		//We retrieve only users with published and not password protected enabled post types

		$enabledPostTypes = $gsg->GetActivePostTypes();

		//Ensure we count at least the posts...
		if(count($enabledPostTypes) == 0) $enabledPostTypes[] = "post";

		$sql = "SELECT DISTINCT
					u.ID,
					u.user_nicename,
					MAX(p.post_modified_gmt) AS last_post
				FROM
					{$wpdb->users} u,
					{$wpdb->posts} p
				WHERE
					p.post_author = u.ID
					AND p.post_status = 'publish'
					AND p.post_type IN('" . implode("','", array_map('esc_sql', $enabledPostTypes)) . "')
					AND p.post_password = ''
				GROUP BY
					u.ID,
					u.user_nicename";

		$authors = $wpdb->get_results($sql);

		if($authors && is_array($authors)) {
			foreach($authors as $author) {
				$url = get_author_posts_url($author->ID, $author->user_nicename);
				$gsg->AddUrl($url, $gsg->GetTimestampFromMySql($author->last_post), $gsg->GetOption("cf_auth"), $gsg->GetOption("pr_auth"));
			}
		}
	}

	/**
	 * Filters the terms query to only include published posts
	 *
	 * @param $selects string[]
	 * @return string[]
	 */
	public function FilterTermsQuery($selects) {
		/** @var $wpdb wpdb */
		global $wpdb;
		$selects[] = "
		( /* ADDED BY XML SITEMAPS */
			SELECT
				UNIX_TIMESTAMP(MAX(p.post_date_gmt)) as _mod_date
			FROM
				{$wpdb->posts} p,
				{$wpdb->term_relationships} r
			WHERE
				p.ID = r.object_id
				AND p.post_status = 'publish'
				AND p.post_password = ''
				AND r.term_taxonomy_id = tt.term_taxonomy_id
		) as _mod_date
		 /* END ADDED BY XML SITEMAPS */
		";

		return $selects;
	}

	/**
	 * Generates the taxonomies sitemap
	 *
	 * @param $gsg GoogleSitemapGenerator
	 * @param $taxonomy string The Taxonomy
	 */
	public function BuildTaxonomies($gsg, $taxonomy) {

		$enabledTaxonomies = $this->GetEnabledTaxonomies($gsg);
		if(in_array($taxonomy, $enabledTaxonomies)) {

			$excludes = array();

			if($taxonomy == "category") {
				$exclCats = $gsg->GetOption("b_exclude_cats"); // Excluded cats
				if($exclCats) $excludes = $exclCats;
			}

			add_filter("get_terms_fields", array($this, "FilterTermsQuery"), 20, 2);
			$terms = get_terms($taxonomy, array("hide_empty" => true, "hierarchical" => false, "exclude" => $excludes));
			remove_filter("get_terms_fields", array($this, "FilterTermsQuery"), 20, 2);

			foreach($terms AS $term) {
				$gsg->AddUrl(get_term_link($term, $term->taxonomy), $term->_mod_date, $gsg->GetOption("cf_tags"), $gsg->GetOption("pr_tags"));
			}
		}
	}

	/**
	 * Returns the enabled taxonomies. Only taxonomies with posts are returned.
	 *
	 * @param GoogleSitemapGenerator $gsg
	 * @return array
	 */
	public function GetEnabledTaxonomies(GoogleSitemapGenerator $gsg) {

		$enabledTaxonomies = $gsg->GetOption("in_tax");
		if($gsg->GetOption("in_tags")) $enabledTaxonomies[] = "post_tag";
		if($gsg->GetOption("in_cats")) $enabledTaxonomies[] = "category";

		$taxList = array();
		foreach($enabledTaxonomies as $taxName) {
			$taxonomy = get_taxonomy($taxName);
			if($taxonomy && wp_count_terms($taxonomy->name, array('hide_empty' => true)) > 0) $taxList[] = $taxonomy->name;
		}
		return $taxList;
	}

	/**
	 * Generates the external sitemap
	 *
	 * @param $gsg GoogleSitemapGenerator
	 */
	public function BuildExternals($gsg) {
		$pages = $gsg->GetPages();
		if($pages && is_array($pages) && count($pages) > 0) {
			foreach($pages AS $page) {
				/** @var $page GoogleSitemapGeneratorPage */
				$gsg->AddUrl($page->GetUrl(), $page->getLastMod(), $page->getChangeFreq(), $page->getPriority());
			}
		}
	}

	/**
	 * Generates the sitemap index
	 *
	 * @param $gsg GoogleSitemapGenerator
	 */
	public function Index($gsg) {
		/**
		 * @var $wpdb wpdb
		 */
		global $wpdb;

		$blogUpdate = strtotime(get_lastpostmodified('gmt'));

		$gsg->AddSitemap("misc", null, $blogUpdate);


		$taxonomies = $this->GetEnabledTaxonomies($gsg);
		foreach($taxonomies AS $tax) {
			$gsg->AddSitemap("tax", $tax, $blogUpdate);
		}

		$pages = $gsg->GetPages();
		if(count($pages) > 0) {
			foreach($pages AS $page) {
				if($page instanceof GoogleSitemapGeneratorPage && $page->GetUrl()) {
					$gsg->AddSitemap("externals", null, $blogUpdate);
					break;
				}
			}
		}

		$enabledPostTypes = $gsg->GetActivePostTypes();

		$hasEnabledPostTypesPosts = false;
		$hasPosts = false;

		if(count($enabledPostTypes) > 0) {

			$excludedPostIDs = $gsg->GetExcludedPostIDs($gsg);
			$exPostSQL = "";
			if(count($excludedPostIDs) > 0) {
				$exPostSQL = "AND p.ID NOT IN (" . implode(",", $excludedPostIDs) . ")";
			}

			$excludedCategoryIDs = $gsg->GetExcludedCategoryIDs($gsg);
			$exCatSQL = "";
			if(count($excludedCategoryIDs) > 0) {
				$exCatSQL = "AND ( p.ID NOT IN ( SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (" . implode(",", $excludedCategoryIDs) . ")))";
			}

			foreach($enabledPostTypes AS $postType) {
				$q = "
					SELECT
						YEAR(p.post_date_gmt) AS `year`,
						MONTH(p.post_date_gmt) AS `month`,
						COUNT(p.ID) AS `numposts`,
						MAX(p.post_modified_gmt) as `last_mod`
					FROM
						{$wpdb->posts} p
					WHERE
						p.post_password = ''
						AND p.post_type = '" . esc_sql($postType) . "'
						AND p.post_status = 'publish'
						$exPostSQL
						$exCatSQL
					GROUP BY
						YEAR(p.post_date_gmt),
						MONTH(p.post_date_gmt)
					ORDER BY
						p.post_date_gmt DESC";

				$posts = $wpdb->get_results($q);

				if($posts) {
					if($postType=="post") $hasPosts = true;

					$hasEnabledPostTypesPosts = true;

					foreach($posts as $post) {
						$gsg->AddSitemap("pt", $postType . "-" . sprintf("%04d-%02d", $post->year, $post->month), $gsg->GetTimestampFromMySql($post->last_mod));
					}
				}
			}
		}

		//Only include authors if there is a public post with a enabled post type
		if($gsg->GetOption("in_auth") && $hasEnabledPostTypesPosts) $gsg->AddSitemap("authors", null, $blogUpdate);

		//Only include archived if there are posts with postType post
		if($gsg->GetOption("in_arch") && $hasPosts) $gsg->AddSitemap("archives", null, $blogUpdate);
	}

	/**
	 * Return the URL to the sitemap related to a specific post
	 *
	 * @param array $urls
	 * @param $gsg GoogleSitemapGenerator
	 * @param $postID int The post ID
	 *
	 * @return string[]
	 */
	public function GetSitemapUrlForPost(array $urls, $gsg, $postID) {
		$post = get_post($postID);
		if($post) {
			$lastModified = $gsg->GetTimestampFromMySql($post->post_modified_gmt);

			$url = $gsg->GetXmlUrl("pt", $post->post_type . "-" . date("Y-m", $lastModified));
			$urls[] = $url;
		}

		return $urls;
	}
}

if(defined("WPINC")) new GoogleSitemapGeneratorStandardBuilder();