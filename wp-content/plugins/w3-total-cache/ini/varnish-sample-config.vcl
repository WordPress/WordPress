backend default {
    .host = "127.0.0.1";
    .port = "8080";
}

acl purge {
    # Web server with plugin which will issue PURGE requests
    "localhost";
}

sub vcl_recv {
    if (req.request == "PURGE") {
        if (!client.ip ~ purge) {
            error 405 "Not allowed.";
        }
        ban("req.url ~ ^" + req.url + "$ && req.http.host == " + req.http.host);
        #Below is used with Varnish version < 3.0. Comment out ban above if using those versions
        #purge("req.url ~ ^" req.url "$ && req.http.host == "req.http.host);
    }

    # Normalize content-encoding
    if (req.http.Accept-Encoding) {
        if (req.url ~ "\.(jpg|png|gif|gz|tgz|bz2|lzma|tbz)(\?.*|)$") {
            remove req.http.Accept-Encoding;
        } elsif (req.http.Accept-Encoding ~ "gzip") {
            set req.http.Accept-Encoding = "gzip";
        } elsif (req.http.Accept-Encoding ~ "deflate") {
            set req.http.Accept-Encoding = "deflate";
        } else {
            remove req.http.Accept-Encoding;
        }
    }

    # Remove cookies and query string for real static files
    if (req.url ~ "^/[^?]+\.(gif|jpg|jpeg|swf|css|js|txt|flv|mp3|mp4|pdf|ico|png|gz|zip|lzma|bz2|tgz|tbz)(\?.*|)$") {
       unset req.http.cookie;
       set req.url = regsub(req.url, "\?.*$", "");
    }

    # Don't cache backend
    if (req.url ~ "wp-(login|admin|comments-post.php|cron.php)") {
        return (pass);
    }

    return (lookup);
}

# Start Varnish version < 3
#sub vcl_fetch {
#    # Don't store backend
#    if (req.url ~ "wp-(login|admin|comments-post.php|cron.php)" || req.url ~ "preview=true" || req.url ~ "xmlrpc.php") {
#        return (pass);
#    }
#
#    set obj.ttl = 24h;
#    return (deliver);
#}
# End Varnish version < 3

# Start Varnish 3.0
sub vcl_fetch {
  # Don't store backend
  if (req.url ~ "wp-(login|admin|comments-post.php|cron.php)" || req.url ~ "preview=true" || req.url ~ "xmlrpc.php") {
    return (hit_for_pass);
  }
  if ( (!(req.url ~ "(wp-(login|admin|comments-post.php|cron.php)|login)")) || (req.request == "GET") ) {
    unset beresp.http.set-cookie;
    set beresp.ttl = 4h;
  }
  if (req.url ~ "\.(gif|jpg|jpeg|swf|css|js|txt|flv|mp3|mp4|pdf|ico|png)(\?.*|)$") {
     set beresp.ttl = 30d;
  } #else {
  # set beresp.do_esi = true;
  #}
}
sub vcl_hit {
        if (req.request == "PURGE") {
                purge;
                error 200 "Purged.";
        }
}

sub vcl_miss {
        if (req.request == "PURGE") {
                purge;
                error 200 "Purged.";
        }
}
# end Varnish 3