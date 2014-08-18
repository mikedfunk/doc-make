<?php
/**
 * make skeleton classes for autocomplete. All credit to original author:
 * Terrence Howard
 *
 * @package DocMake
 * @license MIT
 */
namespace TerrenceHoward\DocMake\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputOption;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

/**
 * DocMakeCommand
 *
 * @author Terrence Howard
 */
class DocMakeCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'doc:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates documentation needed for auto completion.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $path = $this->option('path');

        $this->line("Generating documentation at {$path}.");

        $this->mkdir($path);

        $aliases = $this->alias();

        foreach ($aliases as $alias => $name) {
            $facade = new ReflectionClass($name);

            if ($facade->hasMethod('getFacadeRoot')) {
                $file = fopen($path . '/' . $alias . 'Ups.php', 'w');

                $class = $this->getClass($alias, $name);

                $this->writeClass($file, $class, $alias);

                fclose($file);
            }
        }
    }

    /**
     * getClass
     *
     * @param string $alias
     * @param string $name
     * @return ReflectionClass
     */
    protected function getClass($alias, $name)
    {
        $actual = get_class($name::getFacadeRoot());

        $class = new ReflectionClass($actual);

        return $class;
    }

    /**
     * mkdir
     *
     * @param string $path
     * @return void
     */
    protected function mkdir($path)
    {
        if (file_exists($path)) {
            `rm -r {$path}`;
        }

        mkdir($path);
    }

    /**
     * get all laravel app aliases
     *
     * @return array
     */
    protected function alias()
    {
        return Config::get('app.aliases');
    }

    /**
     * writeClass
     *
     * @param array $file
     * @param ReflectionClass $class
     * @param string $alias
     * @return void
     */
    protected function writeClass($file, $class, $alias)
    {
        $this->write($file, "<?php");
        $this->write(
            $file,
            $this->decorateClassDocBlock(
                $class,
                $class->getDocComment()
            )
        );
        $this->write($file, "class {$alias} {");

        $methods = $class->getMethods();

        foreach ($methods as $method) {
            $this->writeMethod($file, $method);
        }

        $this->write($file, "}");
    }

    /**
     * decorateClassDocBlock
     *
     * @param \ReflectionClass $class
     * @param mixed $docblock
     * @return string
     */
    protected function decorateClassDocBlock(\ReflectionClass $class, $docblock)
    {
        $docblock = preg_replace(
            '/^\/\*+/',
            "/**\n * @see \\{$class->getName()}\n * ",
            $docblock
        );

        if (!$docblock) {
            $docblock = "/**\n * @see \\{$class->getName()}\n */";
        }

        return $docblock;
    }

    /**
     * writeMethod
     *
     * @param array $file
     * @param ReflectionMethod $method
     * @return void
     */
    protected function writeMethod($file, ReflectionMethod $method)
    {
        $name = $method->getName();

        $this->write(
            $file,
            "    " . str_replace("\t", "    ", $method->getDocComment())
        );
        $this->write($file, "    public static function {$name} (", false);

        $contents = file_get_contents($method->getFileName());

        $i = 0;

        do {
            $matches = array();

            $start = $method->getStartLine() - $i;

            $length = $method->getEndLine() - $start + 1 + $i;

            $lines = explode("\n", $contents);

            $line = implode("", array_splice($lines, $start, $length));

            $i++;
        } while (
            !preg_match(
                "/function\s+{$name}\s*\((.*?)\)[^\)]*\{/",
                $line,
                $matches
            )
        );

        $this->write($file, $matches[1], false);

        // $parameters = $method->getParameters();
        //        foreach ($parameters as $key => $parameter) {
        //            $this->write($file, (!$key ? "" : ", "), false);
        //            $this->writeParameter($file, $parameter);
        //        }

        $this->write($file, "){}");
    }

    /**
     * writeParameter
     *
     * @param array $file
     * @param ReflectionParameter $parameter
     * @return void
     */
    protected function writeParameter($file, ReflectionParameter $parameter)
    {
        $this->write($file, "$" . $parameter->getName(), false);

        if ($parameter->isOptional()) {
            $this->write($file, '=null', false);
        }
    }

    /**
     * write
     *
     * @param array $file
     * @param string $data
     * @param bool $endline
     * @return void
     */
    protected function write($file, $data, $endline = true)
    {
        fwrite($file, $data . ($endline ? "\n" : ""));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            //            array('example', InputArgument::REQUIRED, 'An example argument.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            //            array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
            array('path', 'p', InputOption::VALUE_OPTIONAL, 'Desired path for documentation',
            'app/autocomplete'),
        );
    }
}
