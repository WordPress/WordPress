<?php

if (!defined('W3TC')) {
    die();
}

$csstidy_templates = array(
    'highest_compression' => __('Highest (no readability, smallest size)', 'w3-total-cache'),
    'high_compression' => __('High (moderate readability, smaller size)', 'w3-total-cache'),
    'default' => __('Standard (balance between readability and size)', 'w3-total-cache'),
    'low_compression' => __('Low (higher readability)', 'w3-total-cache'),
);

$optimise_shorthands_values = array(
    0 => __('Don\'t optimise', 'w3-total-cache'),
    1 => __('Safe optimisations', 'w3-total-cache'),
    2 => __('All optimisations', 'w3-total-cache')
);

$case_properties_values = array(
    0 => __('None', 'w3-total-cache'),
    1 => __('Lowercase', 'w3-total-cache'),
    2 => __('Uppercase', 'w3-total-cache')
);

$merge_selectors_values = array(
    0 => __('Do not change anything', 'w3-total-cache'),
    1 => __('Only seperate selectors (split at ,)', 'w3-total-cache'),
    2 => __('Merge selectors with the same properties (fast)', 'w3-total-cache')
);

$csstidy_template = $this->_config->get_string('minify.csstidy.options.template');
$optimise_shorthands = $this->_config->get_integer('minify.csstidy.options.optimise_shorthands');
$case_properties = $this->_config->get_integer('minify.csstidy.options.case_properties');
$merge_selectors = $this->_config->get_integer('minify.csstidy.options.merge_selectors');
?>
<tr>
    <th><label for="minify_csstidy_options_template"><?php w3_e_config_label('minify.csstidy.options.template') ?></label></th>
    <td>
        <select id="minify_csstidy_options_template" class="css_enabled" name="minify.csstidy.options.template"
            <?php $this->sealing_disabled('minify') ?>>
            <?php foreach ($csstidy_templates as $csstidy_template_key => $csstidy_template_name): ?>
            <option value="<?php echo esc_attr($csstidy_template_key); ?>"  <?php selected($csstidy_template, $csstidy_template_key); ?>><?php echo $csstidy_template_name; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
<tr>
    <th><label for="minify_csstidy_options_optimise_shorthands"><?php w3_e_config_label('minify.csstidy.options.optimise_shorthands') ?></label></th>
    <td>
        <select id="minify_csstidy_options_optimise_shorthands" class="css_enabled"
            <?php $this->sealing_disabled('minify') ?> name="minify.csstidy.options.optimise_shorthands">
            <?php foreach ($optimise_shorthands_values as $optimise_shorthands_key => $optimise_shorthands_name): ?>
            <option value="<?php echo esc_attr($optimise_shorthands_key); ?>" <?php selected($optimise_shorthands, $optimise_shorthands_key); ?>><?php echo $optimise_shorthands_name; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
<tr>
    <th><label for="minify_csstidy_options_case_properties"><?php w3_e_config_label('minify.csstidy.options.case_properties') ?></label></th>
    <td>
        <select id="minify_csstidy_options_case_properties" class="css_enabled"
            <?php $this->sealing_disabled('minify') ?> name="minify.csstidy.options.case_properties">
            <?php foreach ($case_properties_values as $case_properties_key => $case_properties_name): ?>
            <option value="<?php echo esc_attr($case_properties_key); ?>"  <?php selected($case_properties, $case_properties_key); ?>><?php echo $case_properties_name; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
<tr>
    <th><label for="minify_csstidy_options_merge_selectors"><?php w3_e_config_label('minify.csstidy.options.merge_selectors') ?></label></th>
    <td>
        <select id="minify_csstidy_options_merge_selectors" class="css_enabled"
            <?php $this->sealing_disabled('minify') ?> name="minify.csstidy.options.merge_selectors">
            <?php foreach ($merge_selectors_values as $merge_selectors_key => $merge_selectors_name): ?>
            <option value="<?php echo esc_attr($merge_selectors_key); ?>" <?php selected($merge_selectors, $merge_selectors_key); ?>><?php echo $merge_selectors_name; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
