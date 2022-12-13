<?php

use data\userdata\UserData;

include "lib.php";

UserData::createTable();
//App::createUserDataBase();
App::createPostDataBase();
App::createCommentDataBase();
App::createSupportPostDataBase();