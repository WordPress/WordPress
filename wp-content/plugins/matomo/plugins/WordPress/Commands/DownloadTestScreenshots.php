<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WordPress\Commands;

use Piwik\Container\StaticContainer;
use Piwik\Development;
use Piwik\Filesystem;
use Piwik\Http;
use Piwik\Plugin\ConsoleCommand;
use Matomo\Dependencies\Symfony\Component\Console\Question\ChoiceQuestion;

class DownloadTestScreenshots extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('wordpress:download-test-screenshots');
        $this->addRequiredValueOption('artifact', null,
            'The ID of the screenshot artifacts to download.');
    }

    protected function doExecute(): int
    {
        $input = $this->getInput();
        $output = $this->getOutput();

        $artifactId = $input->getOption('artifact');
        if (empty($artifactId)) {
            $artifactId = $this->pickArtifact();
        }

        $artifactUrl = "https://api.github.com/repos/matomo-org/matomo-for-wordpress/actions/artifacts/$artifactId/zip";

        $output->writeln("Downloading...");
        $localArtifactsPath = $this->downloadArtifacts($artifactId, $artifactUrl);

        $output->writeln("Extracting...");
        $this->extractArtifacts($localArtifactsPath);

        if (is_file($localArtifactsPath)) {
            unlink($localArtifactsPath);
        }

        $output->writeln("Done. The artifacts were extracted to ./tests/e2e/actual.");

        return 0;
    }

    public function isEnabled()
    {
        return Development::isEnabled();
    }

    private function downloadArtifacts($artifactId, $artifactUrl)
    {
        $outputPath = StaticContainer::get('path.tmp') . '/' . $artifactId . '.zip';

        // PHP curl cannot be used due to https://github.com/orgs/community/discussions/88698
        $command = "curl -L '$artifactUrl' --header 'Accept: application/vnd.github+json' --header 'X-GitHub-Api-Version: 2022-11-28' "
            . "--header 'Authorization: Bearer " . $this->getGithubToken() . "' --output '" . $outputPath . "'";
        passthru($command);

        return $outputPath;
    }

    private function extractArtifacts($archivePath)
    {
        $actualFolderPath = PIWIK_INCLUDE_PATH . '/../tests/e2e/actual';
        Filesystem::unlinkRecursive($actualFolderPath, false);

        $command = "unzip -o $archivePath -d " . $actualFolderPath;
        exec($command, $output, $returnCode);
        if ($returnCode) {
            throw new \Exception('unzip failed: ' . implode("\n", $output));
        }
    }

    private function pickArtifact()
    {
        $artifactsApiUrl = 'https://api.github.com/repos/matomo-org/matomo-for-wordpress/actions/artifacts?per_page=100';

        $response = Http::sendHttpRequest($artifactsApiUrl, 3000);
        $response = json_decode($response, true);

        // pick build
        $builds = [];
        foreach ($response['artifacts'] as $artifactInfo) {
            $buildId = $artifactInfo['workflow_run']['id'];
            $branchName = $artifactInfo['workflow_run']['head_branch'];

            $builds["$branchName (workflow run $buildId)"] = true;
        }
        $builds = array_keys($builds);
        $builds = array_slice($builds, 0, 10);

        // hack needed in matomo 5, since helpers can no longer be accessed directly
        $klass = new \ReflectionClass(get_parent_class(get_parent_class(self::class)));
        $property = $klass->getProperty('helperSet');
        $property->setAccessible(true);
        $helperSet = $property->getValue($this);

        $helper = $helperSet->get('question');
        $question = new ChoiceQuestion('Select a build:', $builds);
        $build = $helper->ask($this->getInput(), $this->getOutput(), $question);

        preg_match('/workflow run ([^)]+)\)/', $build, $matches);
        $buildId = $matches[1];

        // pick artifact from build
        $artifacts = [];
        foreach ($response['artifacts'] as $artifactInfo) {
            if ($artifactInfo['workflow_run']['id'] != $buildId) {
                continue;
            }

            // downloading diffs not supported right now
            if (preg_match('/^diff/', $artifactInfo['name'])) {
                continue;
            }

            $artifacts[] = $artifactInfo;
        }
        $artifacts = array_slice($artifacts, 0, 10);

        $artifactNames = array_column($artifacts, 'name');

        $question = new ChoiceQuestion('Select an artifact:', $artifactNames);
        $artifactName = $helper->ask($this->getInput(), $this->getOutput(), $question);

        foreach ($artifacts as $artifactInfo) {
            if ($artifactInfo['name'] == $artifactName) {
                $artifactId = $artifactInfo['id'];
                break;
            }
        }

        return $artifactId;
    }

    private function getGithubToken()
    {
        $token = getenv('GITHUB_TOKEN');
        if (!empty($token)) {
            return $token;
        }

        // quick hack to parse a .env file
        $envFileContents = parse_ini_file(PIWIK_INCLUDE_PATH . '/../.env');
        if (!empty($envFileContents['GITHUB_TOKEN'])) {
            return $envFileContents['GITHUB_TOKEN'];
        }

        throw new \Exception('No github token found. Create one that has the "actions" scope, and set it as the '
            . 'GITHUB_TOKEN environment variable either in your shell or in the root .env file.');
    }
}
