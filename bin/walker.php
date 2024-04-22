<?php

list($AppRoot, $BootRoot, $BDS) = (function(string $PF, string $PU): array {
	return match(TRUE) {
		($PF !== '') => [ dirname($PF), $PU, '/' ],
		default => [ dirname(__FILE__, 2), dirname(__FILE__, 2), DIRECTORY_SEPARATOR ]
	};
})(Phar::Running(FALSE), Phar::Running(TRUE));

require(join(
	$BDS, [ $BootRoot, 'vendor', 'autoload.php' ]
));

exit(Walker\TerminalApp::Realboot([
	'AppRoot'  => Nether\Common\Filesystem\Util::Repath($AppRoot),
	'BootRoot' => $BootRoot
]));
