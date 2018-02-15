<?php

/**
 * Uses swiftlint to check against GitHub Swift code style
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
    );
  }

  protected function getPathArgumentForLinterFuture($path) {
    // swiftlint up to 0.21 automatically searched for the best config
    // but later it switched to use config from the current dir
    // this is a better fix which allows config overwrite in subdirs.
    // Unfortunately there is no better place to plug in than here
    //
    // We start from the analyzed file path and go up to the project
    // root, using the first config available
    $abs_path = FileSystem::resolvePath($path, $this->getProjectRoot());
    $dirs = FileSystem::walkToRoot($abs_path, $this->getProjectRoot());
    foreach ($dirs as $dir) {
      $config_path = implode(DIRECTORY_SEPARATOR,
                             array($dir, '.swiftlint.yml'));
      if (FileSystem::pathExists($config_path)) {
        return csprintf('--config %s --path %s', $config_path, $path);
      }
    }

    return csprintf('--path %s', $path);
  }

  protected function parseLinterOutput($path, $err, $stdout, $stderr) {
    $ok = ($err == 0);

    $lines = phutil_split_lines($stdout, false);

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
