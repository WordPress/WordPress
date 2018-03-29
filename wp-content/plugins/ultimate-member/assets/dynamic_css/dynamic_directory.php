<style type="text/css">
<?php

if ( isset( $css_profile_card_bg ) && ! empty( $css_profile_card_bg ) ) {

print ".um-$form_id .um-member {
	background: $css_profile_card_bg;
}";

}

if ( isset( $css_card_thickness ) && ! empty(  $css_card_thickness  ) ) {

print ".um-$form_id .um-member {
	border-width: $css_card_thickness;
}";

}

if ( isset( $css_profile_card_text ) && ! empty( $css_profile_card_text ) ) {

print ".um-$form_id .um-member-card * {
	color: $css_profile_card_text;
}";

}

if ( isset( $css_card_bordercolor ) && ! empty( $css_card_bordercolor ) ) {

print ".um-$form_id .um-member {
	border-color: $css_card_bordercolor;
}";

}

if ( isset( $css_img_bordercolor ) && ! empty( $css_img_bordercolor ) ) {

print ".um-$form_id .um-member-photo img {
	border-color: $css_img_bordercolor;
}";

}

?>
</style>