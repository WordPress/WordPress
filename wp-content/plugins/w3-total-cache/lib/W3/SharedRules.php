<?php

class W3_SharedRules
{
    /**
     * Return canonical rules
     *
     * @param bool $cdnftp
     * @return string
     */
    public function canonical_without_location($cdnftp = false) {
        $rules = '';

        switch (true) {
            case w3_is_apache():
            case w3_is_litespeed():
                $host = ($cdnftp) ? w3_get_home_domain() : '%{HTTP_HOST}';
                $rules .= "   <IfModule mod_rewrite.c>\n";
                $rules .= "      RewriteEngine On\n";
                $rules .= "      RewriteCond %{HTTPS} !=on\n";
                $rules .= "      RewriteRule .* - [E=CANONICAL:http://$host%{REQUEST_URI},NE]\n";
                $rules .= "      RewriteCond %{HTTPS} =on\n";
                $rules .= "      RewriteRule .* - [E=CANONICAL:https://$host%{REQUEST_URI},NE]\n";
                $rules .= "   </IfModule>\n";
                $rules .= "   <IfModule mod_headers.c>\n";
                $rules .= '      Header set Link "<%{CANONICAL}e>; rel=\"canonical\""' . "\n";
                $rules .= "   </IfModule>\n";
                break;

            case w3_is_nginx():
                $home = ($cdnftp) ? w3_get_home_domain() : '$host';
                $rules .= '   add_header Link "<$scheme://' . $home . '$uri>; rel=\"canonical\"";' . "\n";
                break;
        }

        return $rules;
    }

    /**
     * Returns canonical rules
     *
     * @param bool $cdnftp
     * @return string
     */
    public function canonical($cdnftp = false) {
        $rules = '';

        $mime_types = $this->_get_other_types();
        $extensions = array_keys($mime_types);

        switch (true) {
            case w3_is_apache():
            case w3_is_litespeed():
                $extensions_lowercase = array_map('strtolower', $extensions);
                $extensions_uppercase = array_map('strtoupper', $extensions);
                $rules .= "<FilesMatch \"\\.(" . implode('|', 
                    array_merge($extensions_lowercase, $extensions_uppercase)) . ")$\">\n";
                $rules .= $this->canonical_without_location($cdnftp);
                $rules .= "</FilesMatch>\n";
                break;

            case w3_is_nginx():
                $rules .= "location ~ \.(" . implode('|', $extensions) . ")$ {\n";
                $rules .= $this->canonical_without_location($cdnftp);
                $rules .= "}\n";
                break;
        }

        return $rules;
    }


    /**
     * Returns allow-origin rules
     *
     * @param bool $cdnftp
     * @return string
     */
    public function allow_origin($cdnftp = false) {
        switch (true) {
            case w3_is_apache():
            case w3_is_litespeed():
                $r  = "<IfModule mod_headers.c>\n";
                $r .= "    Header set Access-Control-Allow-Origin \"*\"\n";
                $r .= "</IfModule>\n";

                if (!$cdnftp)
                    return $r;
                else
                    return 
                        "<FilesMatch \"\.(ttf|ttc|otf|eot|woff|font.css)$\">\n" .
                        $r .
                        "</FilesMatch>\n";

            case w3_is_nginx():
                $r = "   add_header Access-Control-Allow-Origin \"*\";\n";

                if (!$cdnftp)
                    return $r;
                else
                    return
                        "location ~ \\.(ttf|ttc|otf|eot|woff|font.css)$ {\n" .
                        $r .
                        "}\n";
            }

        return '';
    }

    /**
     * Returns other mime types
     *
     * @return array
     */
    private function _get_other_types() {
        $mime_types = include W3TC_INC_DIR . '/mime/other.php';
        return $mime_types;
    }

}
