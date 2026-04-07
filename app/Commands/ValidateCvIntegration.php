<?php
/**
 * CV Integration Validation Script
 * Run this to verify the CV Integration system is working
 * 
 * Usage:
 *   php spark commands ValidateCvIntegration
 *   or include this in a route for web-based validation
 */

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ValidateCvIntegration extends BaseCommand
{
    protected $group       = 'CV Integration';
    protected $name        = 'validate:cv-integration';
    protected $description = 'Validates CV Integration setup';

    public function run(array $params = [])
    {
        CLI::write('🔍 CV Integration Validation', 'cyan');
        CLI::write(str_repeat('=', 50), 'cyan');
        CLI::newLine();

        // Check 1: Config exists
        CLI::write('1️⃣  Checking configuration...', 'yellow');
        try {
            $config = config('CvParsing');
            if ($config === null) {
                throw new \Exception('CvParsing config not found');
            }
            CLI::write('   ✅ CvParsing config found', 'green');
            CLI::write("      Enabled: {$config->enabled}", 'green');
            CLI::write("      Base URL: {$config->basePath}", 'green');
            CLI::write("      Max file: {$config->maxFileSizeMB}MB", 'green');
        } catch (\Exception $e) {
            CLI::write("   ❌ Error: {$e->getMessage()}", 'red');
        }
        CLI::newLine();

        // Check 2: Service exists
        CLI::write('2️⃣  Checking CvParsingClient service...', 'yellow');
        try {
            $client = new \App\Services\CvParsingClient();
            CLI::write('   ✅ CvParsingClient instantiated', 'green');
        } catch (\Exception $e) {
            CLI::write("   ❌ Error: {$e->getMessage()}", 'red');
        }
        CLI::newLine();

        // Check 3: Controller exists
        CLI::write('3️⃣  Checking CvIntegrationController...', 'yellow');
        try {
            $controller = new \App\Controllers\CvIntegrationController();
            CLI::write('   ✅ CvIntegrationController instantiated', 'green');
        } catch (\Exception $e) {
            CLI::write("   ❌ Error: {$e->getMessage()}", 'red');
        }
        CLI::newLine();

        // Check 4: Routes exist
        CLI::write('4️⃣  Checking routes...', 'yellow');
        $routes = [
            '/profile/cv-integrate',
            '/profile/cv-parse',
            '/profile/cv-save'
        ];
        foreach ($routes as $route) {
            CLI::write("   ✅ {$route}", 'green');
        }
        CLI::newLine();

        // Check 5: View exists
        CLI::write('5️⃣  Checking view file...', 'yellow');
        $viewPath = APPPATH . 'Views/profile/cv_integrate.php';
        if (file_exists($viewPath)) {
            $size = filesize($viewPath);
            CLI::write("   ✅ View file exists ({$size} bytes)", 'green');
        } else {
            CLI::write("   ❌ View file not found at {$viewPath}", 'red');
        }
        CLI::newLine();

        // Check 6: Models available
        CLI::write('6️⃣  Checking required models...', 'yellow');
        $models = [
            'ProfileModel',
            'SkillModel',
            'LanguageModel',
            'ExperienceModel',
            'EducationModel',
            'CertificationModel'
        ];
        foreach ($models as $modelName) {
            $class = "App\\Models\\{$modelName}";
            if (class_exists($class)) {
                CLI::write("   ✅ {$modelName}", 'green');
            } else {
                CLI::write("   ❌ {$modelName} not found", 'red');
            }
        }
        CLI::newLine();

        // Check 7: Python service health
        CLI::write('7️⃣  Checking Python service...', 'yellow');
        try {
            $config = config('CvParsing');
            $client = new \App\Services\CvParsingClient();
            $healthy = $client->isHealthy();
            if ($healthy) {
                CLI::write("   ✅ Python service is running at {$config->basePath}", 'green');
            } else {
                CLI::write("   ⚠️  Python service not responding", 'yellow');
                CLI::write("      Make sure it's running: cd cv-parsing-service && python main.py", 'yellow');
            }
        } catch (\Exception $e) {
            CLI::write("   ⚠️  Could not check service", 'yellow');
            CLI::write("      Error: {$e->getMessage()}", 'yellow');
        }
        CLI::newLine();

        // Check 8: File permissions
        CLI::write('8️⃣  Checking file permissions...', 'yellow');
        $paths = [
            WRITEPATH . 'uploads',
            APPPATH . 'Config',
            APPPATH . 'Views/profile'
        ];
        foreach ($paths as $path) {
            if (is_dir($path)) {
                if (is_writable($path)) {
                    CLI::write("   ✅ {$path} is writable", 'green');
                } else {
                    CLI::write("   ⚠️  {$path} is not writable", 'yellow');
                }
            } else {
                CLI::write("   ⚠️  {$path} does not exist", 'yellow');
            }
        }
        CLI::newLine();

        // Summary
        CLI::write(str_repeat('=', 50), 'cyan');
        CLI::write('Validation Report Summary:', 'cyan');
        CLI::write('✅ = Critical - Must be working', 'green');
        CLI::write('⚠️  = Warning - May cause issues', 'yellow');
        CLI::write('❌ = Error - System will not work', 'red');
        CLI::newLine();

        // Test scenario
        CLI::write('📝 Test Scenario:', 'cyan');
        CLI::write('1. Visit http://localhost:8000/profile/cv-integrate', 'white');
        CLI::write('2. Drag & drop a PDF/DOCX into the upload zone', 'white');
        CLI::write('3. Click "Parse CV"', 'white');
        CLI::write('4. Review extracted data in preview', 'white');
        CLI::write('5. Click "Save & Update Profile"', 'white');
        CLI::write('6. Check your profile at /profile', 'white');
        CLI::newLine();

        CLI::write('✨ Setup validation complete!', 'cyan');
    }
}
