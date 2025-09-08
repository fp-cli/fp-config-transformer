<?php

use FP_CLI\Tests\TestCase;

/**
 * In PHP 8.0, 8.1, 8.2, `parse_fp_config` failed to parse string constant values that contain double-slashes
 * when there are empty line comments in fp-config.
 *
 * See: https://github.com/fp-cli/fp-config-transformer/issues/47
 */
class EmptyLineCommentTest extends TestCase {

	protected static $test_config_path;
	protected static $config_transformer;

	public static function set_up_before_class() {
		self::$test_config_path = __DIR__ . '/fp-config-test-empty-line-comment.php';

		file_put_contents(
			self::$test_config_path,
			<<<EOF
<?php
// Empty Line Comment
// See: https://github.com/fp-cli/fp-config-transformer/issues/47
//
define( 'FP_HOME', 'https://finpress.org' );
EOF
		);

		self::$config_transformer = new FPConfigTransformer( self::$test_config_path );
	}


	public static function tear_down_after_class() {
		unlink( self::$test_config_path );
	}

	public function testConfigValues() {
		self::$config_transformer->update( 'constant', 'FP_HOME', 'https://finpress.com' );

		require_once self::$test_config_path;

		$this->assertNotSame( 'https://finpress.org', constant( 'FP_HOME' ), 'FP_HOME still contains the original value (https://finpress.org).' );
		$this->assertEquals( 'https://finpress.com', constant( 'FP_HOME' ), 'FP_HOME was not updated to the new value (https://finpress.com).' );
	}
}
