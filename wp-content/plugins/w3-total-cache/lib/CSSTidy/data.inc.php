<?php
/**
 * Various CSS Data for CSSTidy
 *
 * This file is part of CSSTidy.
 *
 * CSSTidy is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * CSSTidy is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CSSTidy; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package csstidy
 * @author Florian Schmitz (floele at gmail dot com) 2005
 */

define('AT_START',    1);
define('AT_END',      2);
define('SEL_START',   3);
define('SEL_END',     4);
define('PROPERTY',    5);
define('VALUE',       6);
define('COMMENT',     7);
define('DEFAULT_AT', 41);

/**
 * All whitespace allowed in CSS
 *
 * @global array $GLOBALS['csstidy']['whitespace']
 * @version 1.0
 */
$GLOBALS['csstidy']['whitespace'] = array(' ',"\n","\t","\r","\x0B");

/**
 * All CSS tokens used by csstidy
 *
 * @global string $GLOBALS['csstidy']['tokens']
 * @version 1.0
 */
$GLOBALS['csstidy']['tokens'] = '/@}{;:=\'"(,\\!$%&)*+.<>?[]^`|~';

/**
 * All CSS units (CSS 3 units included)
 *
 * @see compress_numbers()
 * @global array $GLOBALS['csstidy']['units']
 * @version 1.0
 */
$GLOBALS['csstidy']['units'] = array('in','cm','mm','pt','pc','px','rem','em','%','ex','gd','vw','vh','vm','deg','grad','rad','ms','s','khz','hz');

/**
 * Available at-rules
 *
 * @global array $GLOBALS['csstidy']['at_rules']
 * @version 1.0
 */
$GLOBALS['csstidy']['at_rules'] = array('page' => 'is','font-face' => 'is','charset' => 'iv', 'import' => 'iv','namespace' => 'iv','media' => 'at');

 /**
 * Properties that need a value with unit
 *
 * @todo CSS3 properties
 * @see compress_numbers();
 * @global array $GLOBALS['csstidy']['unit_values']
 * @version 1.2
 */
$GLOBALS['csstidy']['unit_values'] = array ('background', 'background-position', 'border', 'border-top', 'border-right', 'border-bottom', 'border-left', 'border-width',
                                            'border-top-width', 'border-right-width', 'border-left-width', 'border-bottom-width', 'bottom', 'border-spacing', 'font-size',
                                            'height', 'left', 'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left', 'max-height', 'max-width',
                                            'min-height', 'min-width', 'outline-width', 'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
                                            'position', 'right', 'top', 'text-indent', 'letter-spacing', 'word-spacing', 'width');

/**
 * Properties that allow <color> as value
 *
 * @todo CSS3 properties
 * @see compress_numbers();
 * @global array $GLOBALS['csstidy']['color_values']
 * @version 1.0
 */
$GLOBALS['csstidy']['color_values'] = array();
$GLOBALS['csstidy']['color_values'][] = 'background-color';
$GLOBALS['csstidy']['color_values'][] = 'border-color';
$GLOBALS['csstidy']['color_values'][] = 'border-top-color';
$GLOBALS['csstidy']['color_values'][] = 'border-right-color';
$GLOBALS['csstidy']['color_values'][] = 'border-bottom-color';
$GLOBALS['csstidy']['color_values'][] = 'border-left-color';
$GLOBALS['csstidy']['color_values'][] = 'color';
$GLOBALS['csstidy']['color_values'][] = 'outline-color';


/**
 * Default values for the background properties
 *
 * @todo Possibly property names will change during CSS3 development
 * @global array $GLOBALS['csstidy']['background_prop_default']
 * @see dissolve_short_bg()
 * @see merge_bg()
 * @version 1.0
 */
$GLOBALS['csstidy']['background_prop_default'] = array();
$GLOBALS['csstidy']['background_prop_default']['background-image'] = 'none';
$GLOBALS['csstidy']['background_prop_default']['background-size'] = 'auto';
$GLOBALS['csstidy']['background_prop_default']['background-repeat'] = 'repeat';
$GLOBALS['csstidy']['background_prop_default']['background-position'] = '0 0';
$GLOBALS['csstidy']['background_prop_default']['background-attachment'] = 'scroll';
$GLOBALS['csstidy']['background_prop_default']['background-clip'] = 'border';
$GLOBALS['csstidy']['background_prop_default']['background-origin'] = 'padding';
$GLOBALS['csstidy']['background_prop_default']['background-color'] = 'transparent';

/**
 * A list of non-W3C color names which get replaced by their hex-codes
 *
 * @global array $GLOBALS['csstidy']['replace_colors']
 * @see cut_color()
 * @version 1.0
 */
$GLOBALS['csstidy']['replace_colors'] = array();
$GLOBALS['csstidy']['replace_colors']['aliceblue'] = '#F0F8FF';
$GLOBALS['csstidy']['replace_colors']['antiquewhite'] = '#FAEBD7';
$GLOBALS['csstidy']['replace_colors']['aquamarine'] = '#7FFFD4';
$GLOBALS['csstidy']['replace_colors']['azure'] = '#F0FFFF';
$GLOBALS['csstidy']['replace_colors']['beige'] = '#F5F5DC';
$GLOBALS['csstidy']['replace_colors']['bisque'] = '#FFE4C4';
$GLOBALS['csstidy']['replace_colors']['blanchedalmond'] = '#FFEBCD';
$GLOBALS['csstidy']['replace_colors']['blueviolet'] = '#8A2BE2';
$GLOBALS['csstidy']['replace_colors']['brown'] = '#A52A2A';
$GLOBALS['csstidy']['replace_colors']['burlywood'] = '#DEB887';
$GLOBALS['csstidy']['replace_colors']['cadetblue'] = '#5F9EA0';
$GLOBALS['csstidy']['replace_colors']['chartreuse'] = '#7FFF00';
$GLOBALS['csstidy']['replace_colors']['chocolate'] = '#D2691E';
$GLOBALS['csstidy']['replace_colors']['coral'] = '#FF7F50';
$GLOBALS['csstidy']['replace_colors']['cornflowerblue'] = '#6495ED';
$GLOBALS['csstidy']['replace_colors']['cornsilk'] = '#FFF8DC';
$GLOBALS['csstidy']['replace_colors']['crimson'] = '#DC143C';
$GLOBALS['csstidy']['replace_colors']['cyan'] = '#00FFFF';
$GLOBALS['csstidy']['replace_colors']['darkblue'] = '#00008B';
$GLOBALS['csstidy']['replace_colors']['darkcyan'] = '#008B8B';
$GLOBALS['csstidy']['replace_colors']['darkgoldenrod'] = '#B8860B';
$GLOBALS['csstidy']['replace_colors']['darkgray'] = '#A9A9A9';
$GLOBALS['csstidy']['replace_colors']['darkgreen'] = '#006400';
$GLOBALS['csstidy']['replace_colors']['darkkhaki'] = '#BDB76B';
$GLOBALS['csstidy']['replace_colors']['darkmagenta'] = '#8B008B';
$GLOBALS['csstidy']['replace_colors']['darkolivegreen'] = '#556B2F';
$GLOBALS['csstidy']['replace_colors']['darkorange'] = '#FF8C00';
$GLOBALS['csstidy']['replace_colors']['darkorchid'] = '#9932CC';
$GLOBALS['csstidy']['replace_colors']['darkred'] = '#8B0000';
$GLOBALS['csstidy']['replace_colors']['darksalmon'] = '#E9967A';
$GLOBALS['csstidy']['replace_colors']['darkseagreen'] = '#8FBC8F';
$GLOBALS['csstidy']['replace_colors']['darkslateblue'] = '#483D8B';
$GLOBALS['csstidy']['replace_colors']['darkslategray'] = '#2F4F4F';
$GLOBALS['csstidy']['replace_colors']['darkturquoise'] = '#00CED1';
$GLOBALS['csstidy']['replace_colors']['darkviolet'] = '#9400D3';
$GLOBALS['csstidy']['replace_colors']['deeppink'] = '#FF1493';
$GLOBALS['csstidy']['replace_colors']['deepskyblue'] = '#00BFFF';
$GLOBALS['csstidy']['replace_colors']['dimgray'] = '#696969';
$GLOBALS['csstidy']['replace_colors']['dodgerblue'] = '#1E90FF';
$GLOBALS['csstidy']['replace_colors']['feldspar'] = '#D19275';
$GLOBALS['csstidy']['replace_colors']['firebrick'] = '#B22222';
$GLOBALS['csstidy']['replace_colors']['floralwhite'] = '#FFFAF0';
$GLOBALS['csstidy']['replace_colors']['forestgreen'] = '#228B22';
$GLOBALS['csstidy']['replace_colors']['gainsboro'] = '#DCDCDC';
$GLOBALS['csstidy']['replace_colors']['ghostwhite'] = '#F8F8FF';
$GLOBALS['csstidy']['replace_colors']['gold'] = '#FFD700';
$GLOBALS['csstidy']['replace_colors']['goldenrod'] = '#DAA520';
$GLOBALS['csstidy']['replace_colors']['greenyellow'] = '#ADFF2F';
$GLOBALS['csstidy']['replace_colors']['honeydew'] = '#F0FFF0';
$GLOBALS['csstidy']['replace_colors']['hotpink'] = '#FF69B4';
$GLOBALS['csstidy']['replace_colors']['indianred'] = '#CD5C5C';
$GLOBALS['csstidy']['replace_colors']['indigo'] = '#4B0082';
$GLOBALS['csstidy']['replace_colors']['ivory'] = '#FFFFF0';
$GLOBALS['csstidy']['replace_colors']['khaki'] = '#F0E68C';
$GLOBALS['csstidy']['replace_colors']['lavender'] = '#E6E6FA';
$GLOBALS['csstidy']['replace_colors']['lavenderblush'] = '#FFF0F5';
$GLOBALS['csstidy']['replace_colors']['lawngreen'] = '#7CFC00';
$GLOBALS['csstidy']['replace_colors']['lemonchiffon'] = '#FFFACD';
$GLOBALS['csstidy']['replace_colors']['lightblue'] = '#ADD8E6';
$GLOBALS['csstidy']['replace_colors']['lightcoral'] = '#F08080';
$GLOBALS['csstidy']['replace_colors']['lightcyan'] = '#E0FFFF';
$GLOBALS['csstidy']['replace_colors']['lightgoldenrodyellow'] = '#FAFAD2';
$GLOBALS['csstidy']['replace_colors']['lightgrey'] = '#D3D3D3';
$GLOBALS['csstidy']['replace_colors']['lightgreen'] = '#90EE90';
$GLOBALS['csstidy']['replace_colors']['lightpink'] = '#FFB6C1';
$GLOBALS['csstidy']['replace_colors']['lightsalmon'] = '#FFA07A';
$GLOBALS['csstidy']['replace_colors']['lightseagreen'] = '#20B2AA';
$GLOBALS['csstidy']['replace_colors']['lightskyblue'] = '#87CEFA';
$GLOBALS['csstidy']['replace_colors']['lightslateblue'] = '#8470FF';
$GLOBALS['csstidy']['replace_colors']['lightslategray'] = '#778899';
$GLOBALS['csstidy']['replace_colors']['lightsteelblue'] = '#B0C4DE';
$GLOBALS['csstidy']['replace_colors']['lightyellow'] = '#FFFFE0';
$GLOBALS['csstidy']['replace_colors']['limegreen'] = '#32CD32';
$GLOBALS['csstidy']['replace_colors']['linen'] = '#FAF0E6';
$GLOBALS['csstidy']['replace_colors']['magenta'] = '#FF00FF';
$GLOBALS['csstidy']['replace_colors']['mediumaquamarine'] = '#66CDAA';
$GLOBALS['csstidy']['replace_colors']['mediumblue'] = '#0000CD';
$GLOBALS['csstidy']['replace_colors']['mediumorchid'] = '#BA55D3';
$GLOBALS['csstidy']['replace_colors']['mediumpurple'] = '#9370D8';
$GLOBALS['csstidy']['replace_colors']['mediumseagreen'] = '#3CB371';
$GLOBALS['csstidy']['replace_colors']['mediumslateblue'] = '#7B68EE';
$GLOBALS['csstidy']['replace_colors']['mediumspringgreen'] = '#00FA9A';
$GLOBALS['csstidy']['replace_colors']['mediumturquoise'] = '#48D1CC';
$GLOBALS['csstidy']['replace_colors']['mediumvioletred'] = '#C71585';
$GLOBALS['csstidy']['replace_colors']['midnightblue'] = '#191970';
$GLOBALS['csstidy']['replace_colors']['mintcream'] = '#F5FFFA';
$GLOBALS['csstidy']['replace_colors']['mistyrose'] = '#FFE4E1';
$GLOBALS['csstidy']['replace_colors']['moccasin'] = '#FFE4B5';
$GLOBALS['csstidy']['replace_colors']['navajowhite'] = '#FFDEAD';
$GLOBALS['csstidy']['replace_colors']['oldlace'] = '#FDF5E6';
$GLOBALS['csstidy']['replace_colors']['olivedrab'] = '#6B8E23';
$GLOBALS['csstidy']['replace_colors']['orangered'] = '#FF4500';
$GLOBALS['csstidy']['replace_colors']['orchid'] = '#DA70D6';
$GLOBALS['csstidy']['replace_colors']['palegoldenrod'] = '#EEE8AA';
$GLOBALS['csstidy']['replace_colors']['palegreen'] = '#98FB98';
$GLOBALS['csstidy']['replace_colors']['paleturquoise'] = '#AFEEEE';
$GLOBALS['csstidy']['replace_colors']['palevioletred'] = '#D87093';
$GLOBALS['csstidy']['replace_colors']['papayawhip'] = '#FFEFD5';
$GLOBALS['csstidy']['replace_colors']['peachpuff'] = '#FFDAB9';
$GLOBALS['csstidy']['replace_colors']['peru'] = '#CD853F';
$GLOBALS['csstidy']['replace_colors']['pink'] = '#FFC0CB';
$GLOBALS['csstidy']['replace_colors']['plum'] = '#DDA0DD';
$GLOBALS['csstidy']['replace_colors']['powderblue'] = '#B0E0E6';
$GLOBALS['csstidy']['replace_colors']['rosybrown'] = '#BC8F8F';
$GLOBALS['csstidy']['replace_colors']['royalblue'] = '#4169E1';
$GLOBALS['csstidy']['replace_colors']['saddlebrown'] = '#8B4513';
$GLOBALS['csstidy']['replace_colors']['salmon'] = '#FA8072';
$GLOBALS['csstidy']['replace_colors']['sandybrown'] = '#F4A460';
$GLOBALS['csstidy']['replace_colors']['seagreen'] = '#2E8B57';
$GLOBALS['csstidy']['replace_colors']['seashell'] = '#FFF5EE';
$GLOBALS['csstidy']['replace_colors']['sienna'] = '#A0522D';
$GLOBALS['csstidy']['replace_colors']['skyblue'] = '#87CEEB';
$GLOBALS['csstidy']['replace_colors']['slateblue'] = '#6A5ACD';
$GLOBALS['csstidy']['replace_colors']['slategray'] = '#708090';
$GLOBALS['csstidy']['replace_colors']['snow'] = '#FFFAFA';
$GLOBALS['csstidy']['replace_colors']['springgreen'] = '#00FF7F';
$GLOBALS['csstidy']['replace_colors']['steelblue'] = '#4682B4';
$GLOBALS['csstidy']['replace_colors']['tan'] = '#D2B48C';
$GLOBALS['csstidy']['replace_colors']['thistle'] = '#D8BFD8';
$GLOBALS['csstidy']['replace_colors']['tomato'] = '#FF6347';
$GLOBALS['csstidy']['replace_colors']['turquoise'] = '#40E0D0';
$GLOBALS['csstidy']['replace_colors']['violet'] = '#EE82EE';
$GLOBALS['csstidy']['replace_colors']['violetred'] = '#D02090';
$GLOBALS['csstidy']['replace_colors']['wheat'] = '#F5DEB3';
$GLOBALS['csstidy']['replace_colors']['whitesmoke'] = '#F5F5F5';
$GLOBALS['csstidy']['replace_colors']['yellowgreen'] = '#9ACD32';


/**
 * A list of all shorthand properties that are devided into four properties and/or have four subvalues
 *
 * @global array $GLOBALS['csstidy']['shorthands']
 * @todo Are there new ones in CSS3?
 * @see dissolve_4value_shorthands()
 * @see merge_4value_shorthands()
 * @version 1.0
 */
$GLOBALS['csstidy']['shorthands'] = array();
$GLOBALS['csstidy']['shorthands']['border-color'] = array('border-top-color','border-right-color','border-bottom-color','border-left-color');
$GLOBALS['csstidy']['shorthands']['border-style'] = array('border-top-style','border-right-style','border-bottom-style','border-left-style');
$GLOBALS['csstidy']['shorthands']['border-width'] = array('border-top-width','border-right-width','border-bottom-width','border-left-width');
$GLOBALS['csstidy']['shorthands']['margin'] = array('margin-top','margin-right','margin-bottom','margin-left');
$GLOBALS['csstidy']['shorthands']['padding'] = array('padding-top','padding-right','padding-bottom','padding-left');
$GLOBALS['csstidy']['shorthands']['-moz-border-radius'] = 0;

/**
 * All CSS Properties. Needed for csstidy::property_is_next()
 *
 * @global array $GLOBALS['csstidy']['all_properties']
 * @todo Add CSS3 properties
 * @version 1.0
 * @see csstidy::property_is_next()
 */
$GLOBALS['csstidy']['all_properties'] = array();
$GLOBALS['csstidy']['all_properties']['background'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['background-color'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['background-image'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['background-repeat'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['background-attachment'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['background-position'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-top'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-right'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-bottom'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-left'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-color'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-top-color'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-bottom-color'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-left-color'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-right-color'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-style'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-top-style'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-right-style'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-left-style'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-bottom-style'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-width'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-top-width'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-right-width'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-left-width'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-bottom-width'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-collapse'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['border-spacing'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['bottom'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['caption-side'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['content'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['clear'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['clip'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['color'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['counter-reset'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['counter-increment'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['cursor'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['empty-cells'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['display'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['direction'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['float'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['font'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['font-family'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['font-style'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['font-variant'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['font-weight'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['font-stretch'] = 'CSS2.0';
$GLOBALS['csstidy']['all_properties']['font-size-adjust'] = 'CSS2.0';
$GLOBALS['csstidy']['all_properties']['font-size'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['height'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['left'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['line-height'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['list-style'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['list-style-type'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['list-style-image'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['list-style-position'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['margin'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['margin-top'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['margin-right'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['margin-bottom'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['margin-left'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['marks'] = 'CSS1.0,CSS2.0';
$GLOBALS['csstidy']['all_properties']['marker-offset'] = 'CSS2.0';
$GLOBALS['csstidy']['all_properties']['max-height'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['max-width'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['min-height'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['min-width'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['overflow'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['orphans'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['outline'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['outline-width'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['outline-style'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['outline-color'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['padding'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['padding-top'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['padding-right'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['padding-bottom'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['padding-left'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['page-break-before'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['page-break-after'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['page-break-inside'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['page'] = 'CSS2.0';
$GLOBALS['csstidy']['all_properties']['position'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['quotes'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['right'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['size'] = 'CSS1.0,CSS2.0';
$GLOBALS['csstidy']['all_properties']['speak-header'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['table-layout'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['top'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['text-indent'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['text-align'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['text-decoration'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['text-shadow'] = 'CSS2.0';
$GLOBALS['csstidy']['all_properties']['letter-spacing'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['word-spacing'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['text-transform'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['white-space'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['unicode-bidi'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['vertical-align'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['visibility'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['width'] = 'CSS1.0,CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['widows'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['z-index'] = 'CSS1.0,CSS2.0,CSS2.1';
/* Speech */
$GLOBALS['csstidy']['all_properties']['volume'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['speak'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['pause'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['pause-before'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['pause-after'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['cue'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['cue-before'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['cue-after'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['play-during'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['azimuth'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['elevation'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['speech-rate'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['voice-family'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['pitch'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['pitch-range'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['stress'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['richness'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['speak-punctuation'] = 'CSS2.0,CSS2.1';
$GLOBALS['csstidy']['all_properties']['speak-numeral'] = 'CSS2.0,CSS2.1';

/**
 * An array containing all predefined templates.
 *
 * @global array $GLOBALS['csstidy']['predefined_templates']
 * @version 1.0
 * @see csstidy::load_template()
 */
$GLOBALS['csstidy']['predefined_templates']['default'][] = '<span class="at">'; //string before @rule
$GLOBALS['csstidy']['predefined_templates']['default'][] = '</span> <span class="format">{</span>'."\n"; //bracket after @-rule
$GLOBALS['csstidy']['predefined_templates']['default'][] = '<span class="selector">'; //string before selector
$GLOBALS['csstidy']['predefined_templates']['default'][] = '</span> <span class="format">{</span>'."\n"; //bracket after selector
$GLOBALS['csstidy']['predefined_templates']['default'][] = '<span class="property">'; //string before property
$GLOBALS['csstidy']['predefined_templates']['default'][] = '</span><span class="value">'; //string after property+before value
$GLOBALS['csstidy']['predefined_templates']['default'][] = '</span><span class="format">;</span>'."\n"; //string after value
$GLOBALS['csstidy']['predefined_templates']['default'][] = '<span class="format">}</span>'; //closing bracket - selector
$GLOBALS['csstidy']['predefined_templates']['default'][] = "\n\n"; //space between blocks {...}
$GLOBALS['csstidy']['predefined_templates']['default'][] = "\n".'<span class="format">}</span>'. "\n\n"; //closing bracket @-rule
$GLOBALS['csstidy']['predefined_templates']['default'][] = ''; //indent in @-rule
$GLOBALS['csstidy']['predefined_templates']['default'][] = '<span class="comment">'; // before comment
$GLOBALS['csstidy']['predefined_templates']['default'][] = '</span>'."\n"; // after comment
$GLOBALS['csstidy']['predefined_templates']['default'][] = "\n"; // after last line @-rule

$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = '<span class="at">';
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = '</span> <span class="format">{</span>'."\n";
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = '<span class="selector">';
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = '</span><span class="format">{</span>';
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = '<span class="property">';
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = '</span><span class="value">';
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = '</span><span class="format">;</span>';
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = '<span class="format">}</span>';
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = "\n";
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = "\n". '<span class="format">}'."\n".'</span>';
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = '';
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = '<span class="comment">'; // before comment
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = '</span>'; // after comment
$GLOBALS['csstidy']['predefined_templates']['high_compression'][] = "\n";

$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '<span class="at">';
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '</span><span class="format">{</span>';
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '<span class="selector">';
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '</span><span class="format">{</span>';
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '<span class="property">';
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '</span><span class="value">';
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '</span><span class="format">;</span>';
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '<span class="format">}</span>';
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '';
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '<span class="format">}</span>';
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '';
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '<span class="comment">'; // before comment
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '</span>'; // after comment
$GLOBALS['csstidy']['predefined_templates']['highest_compression'][] = '';

$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = '<span class="at">';
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = '</span> <span class="format">{</span>'."\n";
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = '<span class="selector">';
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = '</span>'."\n".'<span class="format">{</span>'."\n";
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = '	<span class="property">';
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = '</span><span class="value">';
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = '</span><span class="format">;</span>'."\n";
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = '<span class="format">}</span>';
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = "\n\n";
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = "\n".'<span class="format">}</span>'."\n\n";
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = '	';
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = '<span class="comment">'; // before comment
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = '</span>'."\n"; // after comment
$GLOBALS['csstidy']['predefined_templates']['low_compression'][] = "\n";

?>