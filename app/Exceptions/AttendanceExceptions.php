<?php

namespace App\Exceptions;

use RuntimeException;

class GeofencingException extends RuntimeException {}
class AlreadyCheckedInException extends RuntimeException {}
class InvalidScanCodeException extends RuntimeException {}
