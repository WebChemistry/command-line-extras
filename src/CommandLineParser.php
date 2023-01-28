<?php declare(strict_types = 1);

namespace WebChemistry\CommandLineExtras;

use Nette\CommandLine\Console;
use Nette\CommandLine\Parser;
use Throwable;

final class CommandLineParser
{

	private Parser $parser;

	private Console $console;

	/**
	 * @param array<string, mixed[]> $arguments
	 * @param array<string, mixed[]> $defaults
	 */
	public function __construct(
		private string $help,
		private array $arguments = [],
		array $defaults = [],
	)
	{
		$this->parser = new Parser($help, array_merge($defaults, $arguments));
		$this->console = new Console();
		$this->console->useColors();
	}

	/**
	 * @param string[]|null $args
	 * @return mixed[]
	 */
	public function parse(?array $args = null): array
	{
		if ($args === null) {
			$args = isset($_SERVER['argv']) ? array_slice($_SERVER['argv'], 1) : [];
		}

		if (in_array('-h', $args, true) || in_array('--help', $args, true)) {
			echo $this->help();

			exit(0);
		}

		try {
			return $this->parser->parse($args);
		} catch (Throwable $exception) {
			echo sprintf("%s\n\n", $this->console->color('red',$exception->getMessage()));
			echo $this->help();

			exit(255);
		}
	}

	/**
	 * @param string[]|null $args
	 */
	private function help(?array $args = null): string
	{
		if ($args === null) {
			$args = $_SERVER['argv'] ?? [];
		}

		$script = $args[0] ?? 'script-name';
		$help = sprintf("Usage: %s %s\n", $script, implode(' ', array_map(
			fn (string $name): string => $this->formatArgument($name),
			array_keys($this->arguments),
		)));
		$help .= ltrim($this->help, "\n");

		return $help;
	}

	private function formatArgument(string $name): string
	{
		$name = sprintf('<%s>', $name);

		if (($this->arguments[$name][Parser::OPTIONAL] ?? false) === true) {
			$name = sprintf('[%s]', $name);
		}

		return $name;
	}

}
