<?php
namespace gossi\codegen\model;

use gossi\docblock\Docblock;
use gossi\codegen\model\parts\InterfacesTrait;
use gossi\codegen\model\parts\ConstantsTrait;
use gossi\codegen\utils\ReflectionUtils;

class PhpInterface extends AbstractPhpStruct implements GenerateableInterface, ConstantsInterface {
	
	use InterfacesTrait;
	use ConstantsTrait;

	public static function fromReflection(\ReflectionClass $ref) {
		$interface = new static();
		$interface->setQualifiedName($ref->name)
			->setConstants($ref->getConstants())
			->setUseStatements(ReflectionUtils::getUseStatements($ref));
		
		$docblock = new Docblock($ref);
		$interface->setDocblock($docblock);
		$interface->setDescription($docblock->getShortDescription());
		$interface->setLongDescription($docblock->getLongDescription());
		
		foreach ($ref->getMethods() as $method) {
			$method = static::createMethod($method);
			$method->setAbstract(false);
			$interface->setMethod($method);
		}
		
		return $interface;
	}

	public function __construct($name = null) {
		parent::__construct($name);
	}

	public function generateDocblock() {
		parent::generateDocblock();
		
		foreach ($this->constants as $constant) {
			$constant->generateDocblock();
		}
	}
}