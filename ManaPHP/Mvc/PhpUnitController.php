<?php

namespace ManaPHP\Mvc {

    class PhpUnitController extends Controller
    {
        //@formatter:off
        public function assertArrayHasKey($key, $array, $message = ''){}
        public function assertArraySubset($subset, $array, $strict = false, $message = ''){}
        public function assertArrayNotHasKey($key, $array, $message = ''){}
        public function assertContains($needle, $haystack, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false){}
        public function assertAttributeContains($needle, $haystackAttributeName, $haystackClassOrObject, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false){}
        public function assertNotContains($needle, $haystack, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false){}
        public function assertAttributeNotContains($needle, $haystackAttributeName, $haystackClassOrObject, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false){}
        public function assertContainsOnly($type, $haystack, $isNativeType = null, $message = ''){}
        public function assertContainsOnlyInstancesOf($class_name, $haystack, $message = ''){}
        public function assertAttributeContainsOnly($type, $haystackAttributeName, $haystackClassOrObject, $isNativeType = null, $message = ''){}
        public function assertNotContainsOnly($type, $haystack, $isNativeType = null, $message = ''){}
        public function assertAttributeNotContainsOnly($type, $haystackAttributeName, $haystackClassOrObject, $isNativeType = null, $message = ''){}
        public function assertCount($expectedCount, $haystack, $message = ''){}
        public function assertAttributeCount($expectedCount, $haystackAttributeName, $haystackClassOrObject, $message = ''){}
        public function assertNotCount($expectedCount, $haystack, $message = ''){}
        public function assertAttributeNotCount($expectedCount, $haystackAttributeName, $haystackClassOrObject, $message = ''){}
        public function assertEquals($expected, $actual, $message = '', $delta = 0.0, $maxDepth = 10, $canonical = false, $ignoreCase = false){}
        public function assertAttributeEquals($expected, $actualAttributeName, $actualClassOrObject, $message = '', $delta = 0.0, $maxDepth = 10, $canonical = false, $ignoreCase = false){}
        public function assertNotEquals($expected, $actual, $message = '', $delta = 0.0, $maxDepth = 10, $canonical = false, $ignoreCase = false){}
        public function assertAttributeNotEquals($expected, $actualAttributeName, $actualClassOrObject, $message = '', $delta = 0.0, $maxDepth = 10, $canonical = false, $ignoreCase = false){}
        public function assertEmpty($actual, $message = ''){}
        public function assertAttributeEmpty($haystackAttributeName, $haystackClassOrObject, $message = ''){}
        public function assertNotEmpty($actual, $message = ''){}
        public function assertAttributeNotEmpty($haystackAttributeName, $haystackClassOrObject, $message = ''){}
        public function assertGreaterThan($expected, $actual, $message = ''){}
        public function assertAttributeGreaterThan($expected, $actualAttributeName, $actualClassOrObject, $message = ''){}
        public function assertGreaterThanOrEqual($expected, $actual, $message = ''){}
        public function assertAttributeGreaterThanOrEqual($expected, $actualAttributeName, $actualClassOrObject, $message = ''){}
        public function assertLessThan($expected, $actual, $message = ''){}
        public function assertAttributeLessThan($expected, $actualAttributeName, $actualClassOrObject, $message = ''){}
        public function assertLessThanOrEqual($expected, $actual, $message = ''){}
        public function assertAttributeLessThanOrEqual($expected, $actualAttributeName, $actualClassOrObject, $message = ''){}
        public function assertFileEquals($expected, $actual, $message = '', $canonical = false, $ignoreCase = false){}
        public function assertFileNotEquals($expected, $actual, $message = '', $canonical = false, $ignoreCase = false){}
        public function assertStringEqualsFile($expectedFile, $actualString, $message = '', $canonical = false, $ignoreCase = false){}
        public function assertStringNotEqualsFile($expectedFile, $actualString, $message = '', $canonical = false, $ignoreCase = false){}
        public function assertFileExists($filename, $message = ''){}
        public function assertFileNotExists($filename, $message = ''){}
        public function assertTrue($condition, $message = ''){}
        public function assertNotTrue($condition, $message = ''){}
        public function assertFalse($condition, $message = ''){}
        public function assertNotFalse($condition, $message = ''){}
        public function assertNotNull($actual, $message = ''){}
        public function assertNull($actual, $message = ''){}
        public function assertClassHasAttribute($attributeName, $className, $message = ''){}
        public function assertClassNotHasAttribute($attributeName, $className, $message = ''){}
        public function assertClassHasStaticAttribute($attributeName, $className, $message = ''){}
        public function assertClassNotHasStaticAttribute($attributeName, $className, $message = ''){}
        public function assertObjectHasAttribute($attributeName, $object, $message = ''){}
        public function assertObjectNotHasAttribute($attributeName, $object, $message = ''){}
        public function assertSame($expected, $actual, $message = ''){}
        public function assertAttributeSame($expected, $actualAttributeName, $actualClassOrObject, $message = ''){}
        public function assertNotSame($expected, $actual, $message = ''){}
        public function assertAttributeNotSame($expected, $actualAttributeName, $actualClassOrObject, $message = ''){}
        public function assertInstanceOf($expected, $actual, $message = ''){}
        public function assertAttributeInstanceOf($expected, $attributeName, $classOrObject, $message = ''){}
        public function assertNotInstanceOf($expected, $actual, $message = ''){}
        public function assertAttributeNotInstanceOf($expected, $attributeName, $classOrObject, $message = ''){}
        public function assertInternalType($expected, $actual, $message = ''){}
        public function assertAttributeInternalType($expected, $attributeName, $classOrObject, $message = ''){}
        public function assertNotInternalType($expected, $actual, $message = ''){}
        public function assertAttributeNotInternalType($expected, $attributeName, $classOrObject, $message = ''){}
        public function assertRegExp($pattern, $string, $message = ''){}
        public function assertNotRegExp($pattern, $string, $message = ''){}
        public function assertSameSize($expected, $actual, $message = ''){}
        public function assertNotSameSize($expected, $actual, $message = ''){}
        public function assertStringMatchesFormat($format, $string, $message = ''){}
        public function assertStringNotMatchesFormat($format, $string, $message = ''){}
        public function assertStringMatchesFormatFile($formatFile, $string, $message = ''){}
        public function assertStringNotMatchesFormatFile($formatFile, $string, $message = ''){}
        public function assertStringStartsWith($prefix, $string, $message = ''){}
        public function assertStringStartsNotWith($prefix, $string, $message = ''){}
        public function assertStringEndsWith($suffix, $string, $message = ''){}
        public function assertStringEndsNotWith($suffix, $string, $message = ''){}
        public function assertXmlFileEqualsXmlFile($expectedFile, $actualFile, $message = ''){}
        public function assertXmlFileNotEqualsXmlFile($expectedFile, $actualFile, $message = ''){}
        public function assertXmlStringEqualsXmlFile($expectedFile, $actualXml, $message = ''){}
        public function assertXmlStringNotEqualsXmlFile($expectedFile, $actualXml, $message = ''){}
        public function assertXmlStringEqualsXmlString($expectedXml, $actualXml, $message = ''){}
        public function assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, $message = ''){}
        public function assertEqualXMLStructure(\DOMElement $expectedElement, \DOMElement $actualElement, $checkAttributes = false, $message = ''){}
        public function assertSelectCount($selector, $count, $actual, $message = '', $isHtml = true){}
        public function assertSelectRegExp($selector, $pattern, $count, $actual, $message = '', $isHtml = true){}
        public function assertSelectEquals($selector, $content, $count, $actual, $message = '', $isHtml = true){}
        public function assertTag($matcher, $actual, $message = '', $isHtml = true){}
        public function assertNotTag($matcher, $actual, $message = '', $isHtml = true){}
        public function assertThat($value, $constraint, $message = ''){}
        public function assertJson($actualJson, $message = ''){}
        public function assertJsonStringEqualsJsonString($expectedJson, $actualJson, $message = ''){}
        public function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, $message = ''){}
        public function assertJsonStringEqualsJsonFile($expectedFile, $actualJson, $message = ''){}
        public function assertJsonStringNotEqualsJsonFile($expectedFile, $actualJson, $message = ''){}
        public function assertJsonFileNotEqualsJsonFile($expectedFile, $actualFile, $message = ''){}
        public function assertJsonFileEqualsJsonFile($expectedFile, $actualFile, $message = ''){}
        public function logicalAnd(){}
        public function logicalOr(){}
        public function logicalNot($constraint){}
        public function logicalXor(){}
        public function anything(){}
        public function isTrue(){}
        public function callback($callback){}
        public function isFalse(){}
        public function isJson(){}
        public function isNull(){}
        public function attribute($constraint, $attributeName){}
        public function contains($value, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false){}
        public function containsOnly($type){}
        public function containsOnlyInstancesOf($class_name){}
        public function arrayHasKey($key){}
        public function equalTo($value, $delta = 0.0, $maxDepth = 10, $canonical = false, $ignoreCase = false){}
        public function attributeEqualTo($attributeName, $value, $delta = 0.0, $maxDepth = 10, $canonical = false, $ignoreCase = false){}
        public function isEmpty(){}
        public function fileExists(){}
        public function greaterThan($value){}
        public function greaterThanOrEqual($value){}
        public function classHasAttribute($attributeName){}
        public function classHasStaticAttribute($attributeName){}
        public function objectHasAttribute($attributeName){}
        public function identicalTo($value){}
        public function isInstanceOf($className){}
        public function isType($type){}
        public function lessThan($value){}
        public function lessThanOrEqual($value){}
        public function matchesRegularExpression($pattern){}
        public function matches($string){}
        public function stringStartsWith($prefix){}
        public function stringContains($string, $case = true){}
        public function stringEndsWith($suffix){}
        public function countOf($count){}
        public function fail($message = ''){}
        public function readAttribute($classOrObject, $attributeName){}
        public function getStaticAttribute($className, $attributeName){}
        public function getObjectAttribute($object, $attributeName){}
        public function markTestIncomplete($message = ''){}
        public function markTestSkipped($message = ''){}
        public function getCount(){}
        public function resetCount(){}
        //@formatter:on
    }
}