; <?php exit; ?> DO NOT REMOVE THIS LINE
; Configuration settings which are applied to all Piwik instances.

[database]
adapter = WordPress

[Tracker]
tracker_cache_file_ttl = 4385

[General]
enable_update_communication = 0
enable_auto_update = 0
enable_plugins_admin = 0
enable_sites_admin = 0
enable_users_admin = 0
enable_geolocation_admin = 0
enable_marketplace = 0
enable_custom_logo = 0
enable_installer = 0
enable_plugin_upload = 0
enable_sql_optimize_queries = 0
enable_general_settings_admin = 0
enable_browser_archiving_triggering = 0
time_before_today_archive_considered_outdated = 1800
time_before_week_archive_considered_outdated = 3500
time_before_month_archive_considered_outdated = 7100
time_before_year_archive_considered_outdated = 14300
time_before_range_archive_considered_outdated = 3600
enable_load_data_infile = 0
enable_tracking_failures_notification = 0
live_widget_refresh_after_seconds = 30
enable_referrer_definition_syncs = 0

[GeoIp2]
geoip2usecustom = 0
geoip2var_country_code = "MM_COUNTRY_CODE"
geoip2var_country_name = "MM_COUNTRY_NAME"
geoip2var_region_code = "MM_REGION_CODE"
geoip2var_region_name = "MM_REGION_NAME"
geoip2var_lat = "MM_LATITUDE"
geoip2var_long = "MM_LONGITUDE"
geoip2var_postal_code = "MM_POSTAL_CODE"
geoip2var_city_name = "MM_CITY_NAME"
geoip2var_isp = "MM_ISP"
geoip2var_org = "MM_ORG"
geoip2var_continent_code = "MM_CONTINENT_CODE"
geoip2var_continent_name = "MM_CONTINENT_NAME"

[Login]
enableBruteForceDetection = 0
whitelisteBruteForceIps = []
blacklistedBruteForceIps = []
maxAllowedRetries = 20
allowedRetriesTimeRange = 60

[PrivacyManager]
showInEmbeddedWidgets = 0

[TagManager]
environments[] = 'live'
