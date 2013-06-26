<?php
// Fill in your Wolfram Alpha ID here. You can get one from http://www.wolframalpha.com/.
define(WRA_API_ID, "");
if (WRA_API_ID == null) {
        exit("\033[0;31mError (1): \033[0mNo Wolfram Alpha App ID detected...Please get one from http://www.wolframalpha.com/\n");
}

@file_get_contents("http://www.google.com") or exit("\033[0;31mError (2): \033[0mNo active internet connection detected!\n");

// Detects if the Apple voice Samantha voice is installed
$voices = shell_exec("/usr/bin/say -v ?");
if (strpos($voices, "Samantha") != null) {
        $voice = "-v Samantha ";
} else {
        $voice = null;
        echo "\033[0;31mWarning: \033[0mSamantha (Siri) voice was not detected. Using default voice...\n";
}

$profile = file_get_contents("/users/" . get_current_user() . "/.profile");
if (strpos($profile, "siri()") == null) {
        echo "\nInstalling Siri...\n";
        $profile .= "\n\nsiri() {\nphp ~/siri.php\n}\n";
        file_put_contents("/users/" . get_current_user() . "/.profile", $profile);
        echo "\nSiri was installed successfully!\n";
        echo "\nYou can access siri by typing \"siri\" in your terminal.\n";
}

echo "Question: ";
$stdin = stripslashes(read_stdin());
$explode = explode(" ", $stdin);
$action = $explode[0];
unset($explode[0]);

if (in_array(strtolower($action), array("open", "launch"))) {
        echo "Answer  : " . ucwords($action) . "ing " . implode(" ", $explode) . "\n";
        shell_exec("/usr/bin/say " . $voice . " " . ucwords($action) . "ing '" . implode(" ", $explode) . "' & open -a '" . implode(" ", $explode) . "'");
        die;
}

$query = rawurlencode($stdin);
$result = trim(file_get_contents("http://guysthatcode.com/projects/siri/?query=" . $query . "&appid=" . WRA_API_ID));
if ($result == null || $result == "(data not available)") {
        echo "Answer  : n/a\n";
        shell_exec("/usr/bin/say " . $voice . " I am sorry, I do not have an answer for that.");
        die;
} else {
        echo "Answer  : " . $result . "\n";
        shell_exec("/usr/bin/say " . $voice . " '" . addslashes(trim($result)) . "'");
}
function read_stdin() {
        $fr=fopen("php://stdin","r");   // open our file pointer to read from stdin
        $input = fgets($fr);
        $input = rtrim($input);         // trim any trailing spaces.
        fclose ($fr);                   // close the file handle
        return $input;                  // return the text entered
}
?>
