<?php

use data\userdata\UserData;

include "lib.php";
App::init();

UserData::createTable();
//App::createUserDataBase();
App::createPostDataBase();
App::createCommentDataBase();
App::createSupportPostDataBase();