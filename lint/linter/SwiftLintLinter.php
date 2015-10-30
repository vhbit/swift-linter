<?php

/**
 * Uses the clang format to format C/C++/Obj-C code
 */
final class SwiftLintLinter extends ArcanistExternalLinter {

  public function getInfoName() {
    return 'swift';
  }

  public function getInfoURI() {
    return '';
  }

  public function getInfoDescription() {
    return pht('Uses swiftlint for processing specified files.');
  }

  public function getLinterName() {
    return 'swift-lint';
  }

  public function getLinterConfigurationName() {
    return 'swift-lint';
  }

  public function getLinterConfigurationOptions() {
    $options = array(
    );

    return $options + parent::getLinterConfigurationOptions();
  }

  public function getDefaultBinary() {
    return 'swiftlint';
  }

  public function getInstallInstructions() {
    return pht(
        'Install swift lint using `%s`.',
        'brew install swiftlint');
  }

  public function shouldExpectCommandErrors() {
    return true;
  }

  protected function getMandatoryFlags() {
    return array(
        'lint',
        '--path',
    );
  }

  protected function parseLinterOutput($path, $err, $stdout, $stderr) {
    $ok = ($err == 0);

    $lines = phutil_split_lines($stderr, false);

    $messages = array();
    foreach ($lines as $line) {
      $matches = null;
      if (!preg_match('/^(.*?):(\d+):((\d+):)? (\S+): ((\s|\w)+): (.*)$/', $line, $matches)) {
        continue;
      }

      foreach ($matches as $key => $match) {
        $matches[$key] = trim($match);
      }

      $message = new ArcanistLintMessage();
      $message->setPath($path);
      $message->setLine($matches[2]);
      if ($matches[4] != '') {
        $message->setChar($matches[4]);
      }
      $message->setCode($this->getLinterName());
      $message->setName($matches[6]);
      $message->setDescription($matches[8]);
      $message->setSeverity($this->getLintMessageSeverity($matches[5]));

      $messages[] = $message;
    }

    return $messages;
  }

  protected function getDefaultMessageSeverity($code) {
    if ($code == 'error') {
      return ArcanistLintSeverity::SEVERITY_ERROR;
    } else {
      return ArcanistLintSeverity::SEVERITY_WARNING;
    }
  }
}

?>
