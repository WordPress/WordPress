<?php

function the_author() {
    global $id, $authordata;
    $i = $authordata->user_idmode;
    if ($i == 'nickname')    echo $authordata->user_nickname;
    if ($i == 'login')    echo $authordata->user_login;
    if ($i == 'firstname')    echo $authordata->user_firstname;
    if ($i == 'lastname')    echo $authordata->user_lastname;
    if ($i == 'namefl')    echo $authordata->user_firstname.' '.$authordata->user_lastname;
    if ($i == 'namelf')    echo $authordata->user_lastname.' '.$authordata->user_firstname;
    if (!$i) echo $authordata->user_nickname;
}
function the_author_description() {
    global $authordata;
    echo $authordata->user_description;
}
function the_author_login() {
    global $id,$authordata;    echo $authordata->user_login;
}

function the_author_firstname() {
    global $id,$authordata;    echo $authordata->user_firstname;
}

function the_author_lastname() {
    global $id,$authordata;    echo $authordata->user_lastname;
}

function the_author_nickname() {
    global $id,$authordata;    echo $authordata->user_nickname;
}

function the_author_ID() {
    global $id,$authordata;    echo $authordata->ID;
}

function the_author_email() {
    global $id,$authordata;    echo antispambot($authordata->user_email);
}

function the_author_url() {
    global $id,$authordata;    echo $authordata->user_url;
}

function the_author_icq() {
    global $id,$authordata;    echo $authordata->user_icq;
}

function the_author_aim() {
    global $id,$authordata;    echo str_replace(' ', '+', $authordata->user_aim);
}

function the_author_yim() {
    global $id,$authordata;    echo $authordata->user_yim;
}

function the_author_msn() {
    global $id,$authordata;    echo $authordata->user_msn;
}

function the_author_posts() {
    global $id,$postdata;    $posts=get_usernumposts($post->post_author);    echo $posts;
}

?>