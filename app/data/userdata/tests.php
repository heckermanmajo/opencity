<?php

use data\userdata\UserData;

UserData::createTestData();

echo "\n";
echo "\n";
echo "\n";
echo "\n";
echo "Start tests\n";
echo "\n";
echo "\n";

foreach (UserData::$test_user_data as $user_data) {
  $user_data->save();
  echo "User created: " . $user_data->username . ": " . $user_data->id;
}

$counter = 1;
foreach (UserData::$test_user_data as $user_data) {
  assert(
    $user_data->id === $counter,
    (
      "id of user_data is not correct id: "
      . $user_data->id
      . " counter: "
      . $counter
    )
  );
  $counter++;
}

// get all users and compare them to the test data
$users = UserData::getAllUsers();

foreach ($users as $user) {
  $user_data = UserData::$test_user_data[$user->id - 1];
  assert(
    $user->id === $user_data->id,
    "user id is not correct"
  );
  assert(
    $user_data->username === $user->username,
    "username of user_data is not correct ". $user_data->username . " " . $user->username
  );
  assert(
    $user_data->email === $user->email,
    "email of user_data is not correct ". $user_data->email . " " . $user->email
  );
  assert(
    $user_data->password === $user->password,
    "password of user_data is not correct ". $user_data->password . " " . $user->password
  );
  assert(
    $user_data->verified === $user->verified,
    "verified of user_data is not correct ". $user_data->verified . " " . $user->verified
  );
  assert(
    $user_data->verification_code === $user->verification_code,
    "verification_code of user_data is not correct ". $user_data->verification_code . " " . $user->verification_code
  );
}

echo "\n";

echo "Tests for UserData finished\n";