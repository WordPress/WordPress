<?php

namespace {
    /**
     * @filesource src/Resources/data/configuration.php
     *
     * All the class constants that were previously in class files are moved here into a single file.
     */
    /**
     * pBubble
     */
    \define("BUBBLE_SHAPE_ROUND", 700001);
    \define("BUBBLE_SHAPE_SQUARE", 700002);
    /**
     * pData
     */
    /* Axis configuration */
    \define("AXIS_FORMAT_DEFAULT", 680001);
    \define("AXIS_FORMAT_TIME", 680002);
    \define("AXIS_FORMAT_DATE", 680003);
    \define("AXIS_FORMAT_METRIC", 680004);
    \define("AXIS_FORMAT_CURRENCY", 680005);
    \define("AXIS_FORMAT_TRAFFIC", 680006);
    \define("AXIS_FORMAT_CUSTOM", 680007);
    /* Axis position */
    \define("AXIS_POSITION_LEFT", 681001);
    \define("AXIS_POSITION_RIGHT", 681002);
    \define("AXIS_POSITION_TOP", 681001);
    \define("AXIS_POSITION_BOTTOM", 681002);
    /* Families of data points */
    \define("SERIE_SHAPE_FILLEDCIRCLE", 681011);
    \define("SERIE_SHAPE_FILLEDTRIANGLE", 681012);
    \define("SERIE_SHAPE_FILLEDSQUARE", 681013);
    \define("SERIE_SHAPE_FILLEDDIAMOND", 681017);
    \define("SERIE_SHAPE_CIRCLE", 681014);
    \define("SERIE_SHAPE_TRIANGLE", 681015);
    \define("SERIE_SHAPE_SQUARE", 681016);
    \define("SERIE_SHAPE_DIAMOND", 681018);
    /* Axis position */
    \define("AXIS_X", 682001);
    \define("AXIS_Y", 682002);
    /* Define value limits */
    \define("ABSOLUTE_MIN", -10000000000000);
    \define("ABSOLUTE_MAX", 10000000000000);
    /* Replacement to the PHP null keyword */
    \define("VOID", 0.123456789);
    /* Euro symbol for GD fonts */
    \define("EURO_SYMBOL", "&#8364;");
    /**
     * pDraw
     */
    \define("DIRECTION_VERTICAL", 690001);
    \define("DIRECTION_HORIZONTAL", 690002);
    \define("SCALE_POS_LEFTRIGHT", 690101);
    \define("SCALE_POS_TOPBOTTOM", 690102);
    \define("SCALE_MODE_FLOATING", 690201);
    \define("SCALE_MODE_START0", 690202);
    \define("SCALE_MODE_ADDALL", 690203);
    \define("SCALE_MODE_ADDALL_START0", 690204);
    \define("SCALE_MODE_MANUAL", 690205);
    \define("SCALE_SKIP_NONE", 690301);
    \define("SCALE_SKIP_SAME", 690302);
    \define("SCALE_SKIP_NUMBERS", 690303);
    \define("TEXT_ALIGN_TOPLEFT", 690401);
    \define("TEXT_ALIGN_TOPMIDDLE", 690402);
    \define("TEXT_ALIGN_TOPRIGHT", 690403);
    \define("TEXT_ALIGN_MIDDLELEFT", 690404);
    \define("TEXT_ALIGN_MIDDLEMIDDLE", 690405);
    \define("TEXT_ALIGN_MIDDLERIGHT", 690406);
    \define("TEXT_ALIGN_BOTTOMLEFT", 690407);
    \define("TEXT_ALIGN_BOTTOMMIDDLE", 690408);
    \define("TEXT_ALIGN_BOTTOMRIGHT", 690409);
    \define("POSITION_TOP", 690501);
    \define("POSITION_BOTTOM", 690502);
    \define("LABEL_POS_LEFT", 690601);
    \define("LABEL_POS_CENTER", 690602);
    \define("LABEL_POS_RIGHT", 690603);
    \define("LABEL_POS_TOP", 690604);
    \define("LABEL_POS_BOTTOM", 690605);
    \define("LABEL_POS_INSIDE", 690606);
    \define("LABEL_POS_OUTSIDE", 690607);
    \define("ORIENTATION_HORIZONTAL", 690701);
    \define("ORIENTATION_VERTICAL", 690702);
    \define("ORIENTATION_AUTO", 690703);
    \define("LEGEND_NOBORDER", 690800);
    \define("LEGEND_BOX", 690801);
    \define("LEGEND_ROUND", 690802);
    \define("LEGEND_VERTICAL", 690901);
    \define("LEGEND_HORIZONTAL", 690902);
    \define("LEGEND_FAMILY_BOX", 691051);
    \define("LEGEND_FAMILY_CIRCLE", 691052);
    \define("LEGEND_FAMILY_LINE", 691053);
    \define("DISPLAY_AUTO", 691001);
    \define("DISPLAY_MANUAL", 691002);
    \define("LABELING_ALL", 691011);
    \define("LABELING_DIFFERENT", 691012);
    \define("BOUND_MIN", 691021);
    \define("BOUND_MAX", 691022);
    \define("BOUND_BOTH", 691023);
    \define("BOUND_LABEL_POS_TOP", 691031);
    \define("BOUND_LABEL_POS_BOTTOM", 691032);
    \define("BOUND_LABEL_POS_AUTO", 691033);
    \define("CAPTION_LEFT_TOP", 691041);
    \define("CAPTION_RIGHT_BOTTOM", 691042);
    \define("GRADIENT_SIMPLE", 691051);
    \define("GRADIENT_EFFECT_CAN", 691052);
    \define("LABEL_TITLE_NOBACKGROUND", 691061);
    \define("LABEL_TITLE_BACKGROUND", 691062);
    \define("LABEL_POINT_NONE", 691071);
    \define("LABEL_POINT_CIRCLE", 691072);
    \define("LABEL_POINT_BOX", 691073);
    \define("ZONE_NAME_ANGLE_AUTO", 691081);
    \define("PI", 3.14159265);
    \define("ALL", 69);
    \define("NONE", 31);
    \define("AUTO", 690000);
    \define("OUT_OF_SIGHT", -10000000000000);
    /**
     * pImage
     */
    /* Image map handling */
    \define("IMAGE_MAP_STORAGE_FILE", 680001);
    \define("IMAGE_MAP_STORAGE_SESSION", 680002);
    /* Last generated chart layout */
    \define("CHART_LAST_LAYOUT_REGULAR", 680011);
    \define("CHART_LAST_LAYOUT_STACKED", 680012);
    /* ImageMap string delimiter */
    \define("IMAGE_MAP_DELIMITER", \chr(1));
    /**
     * pIndicator
     */
    \define("INDICATOR_CAPTION_DEFAULT", 700001);
    \define("INDICATOR_CAPTION_EXTENDED", 700002);
    \define("INDICATOR_CAPTION_INSIDE", 700011);
    \define("INDICATOR_CAPTION_BOTTOM", 700012);
    \define("INDICATOR_VALUE_BUBBLE", 700021);
    \define("INDICATOR_VALUE_LABEL", 700022);
    /**
     * pPie
     */
    /* Class return codes */
    \define("PIE_NO_ABSCISSA", 140001);
    \define("PIE_NO_DATASERIE", 140002);
    \define("PIE_SUMISNULL", 140003);
    \define("PIE_RENDERED", 140000);
    \define("PIE_LABEL_COLOR_AUTO", 140010);
    \define("PIE_LABEL_COLOR_MANUAL", 140011);
    \define("PIE_VALUE_NATURAL", 140020);
    \define("PIE_VALUE_PERCENTAGE", 140021);
    \define("PIE_VALUE_INSIDE", 140030);
    \define("PIE_VALUE_OUTSIDE", 140031);
    /**
     * pRadar
     */
    \define("SEGMENT_HEIGHT_AUTO", 690001);
    \define("RADAR_LAYOUT_STAR", 690011);
    \define("RADAR_LAYOUT_CIRCLE", 690012);
    \define("RADAR_LABELS_ROTATED", 690021);
    \define("RADAR_LABELS_HORIZONTAL", 690022);
    /**
     * pScatter
     */
    \define("SCATTER_MISSING_X_SERIE", 190001);
    \define("SCATTER_MISSING_Y_SERIE", 190002);
    /**
     * pSplit
     */
    \define("TEXT_POS_TOP", 690001);
    \define("TEXT_POS_RIGHT", 690002);
    /**
     * pSpring
     */
    \define("NODE_TYPE_FREE", 690001);
    \define("NODE_TYPE_CENTRAL", 690002);
    \define("NODE_SHAPE_CIRCLE", 690011);
    \define("NODE_SHAPE_TRIANGLE", 690012);
    \define("NODE_SHAPE_SQUARE", 690013);
    \define("ALGORITHM_RANDOM", 690021);
    \define("ALGORITHM_WEIGHTED", 690022);
    \define("ALGORITHM_CIRCULAR", 690023);
    \define("ALGORITHM_CENTRAL", 690024);
    \define("LABEL_CLASSIC", 690031);
    \define("LABEL_LIGHT", 690032);
    /**
     * pStock
     */
    \define("STOCK_MISSING_SERIE", 180001);
    /**
     * pSurface
     */
    \define("UNKNOWN", 0.123456789);
    \define("IGNORED", -1);
    \define("LABEL_POSITION_LEFT", 880001);
    \define("LABEL_POSITION_RIGHT", 880002);
    \define("LABEL_POSITION_TOP", 880003);
    \define("LABEL_POSITION_BOTTOM", 880004);
}
