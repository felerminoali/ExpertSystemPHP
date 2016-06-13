<?php
function db_connect() {
  $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
  mysql_select_db(DB_NAME);
}

function db_close() {
  mysql_close();
}


