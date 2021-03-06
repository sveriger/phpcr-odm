<?php

namespace Doctrine\Tests\ODM\PHPCR\Mapping;

use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadataFactory;
use Doctrine\ODM\PHPCR\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Persistence\Mapping\RuntimeReflectionService;

abstract class AbstractMappingDriverTest extends \PHPUnit_Framework_TestCase
{
    abstract protected function loadDriver();
    abstract protected function loadDriverForTestMappingDocuments();

    protected function ensureIsLoaded($entityClassName)
    {
        new $entityClassName;
    }

    /**
     * Returns a ClassMetadata objet for the given class, loaded using the driver associated with a concrete child
     * of this class.
     *
     * @param string $className
     * @return \Doctrine\ODM\PHPCR\Mapping\ClassMetadata
     */
    protected function loadMetadataForClassname($className)
    {
        $mappingDriver = $this->loadDriver();

        $class = new ClassMetadata($className);
        $class->initializeReflection(new RuntimeReflectionService());
        $mappingDriver->loadMetadataForClass($className, $class);

        return $class;
    }

    public function testLoadMetadataForNonDocumentThrowsException()
    {
        $cm = new ClassMetadata('stdClass');
        $cm->initializeReflection(new RuntimeReflectionService());

        $driver = $this->loadDriver();

        $this->setExpectedException('Doctrine\ODM\PHPCR\Mapping\MappingException');
        $driver->loadMetadataForClass('stdClass', $cm);
    }

    public function testGetAllClassNamesIsIdempotent()
    {
        $driver = $this->loadDriverForTestMappingDocuments();
        $original = $driver->getAllClassNames();

        $driver = $this->loadDriverForTestMappingDocuments();
        $afterTestReset = $driver->getAllClassNames();

        $this->assertEquals($original, $afterTestReset);
    }

    public function testGetAllClassNamesReturnsAlreadyLoadedClassesIfAppropriate()
    {
        $rightClassName = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\FieldMappingObject';
        $this->ensureIsLoaded($rightClassName);

        $driver = $this->loadDriverForTestMappingDocuments();
        $classes = $driver->getAllClassNames();

        $this->assertContains($rightClassName, $classes);
    }

    public function testGetAllClassNamesReturnsOnlyTheAppropriateClasses()
    {
        $extraneousClassName = 'Doctrine\Tests\Models\ECommerce\ECommerceCart';
        $this->ensureIsLoaded($extraneousClassName);

        $driver = $this->loadDriverForTestMappingDocuments();
        $classes = $driver->getAllClassNames();

        $this->assertNotContains($extraneousClassName, $classes);
    }

    /**
     * @covers Doctrine\ODM\PHPCR\Mapping\Driver\XmlDriver::loadMetadataForClass
     * @covers Doctrine\ODM\PHPCR\Mapping\Driver\YamlDriver::loadMetadataForClass
     * @covers Doctrine\ODM\PHPCR\Mapping\Driver\AnnotationDriver::loadMetadataForClass
     */
    public function testLoadFieldMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\FieldMappingObject';
        return $this->loadMetadataForClassName($className);
    }

    /**
     * @depends testLoadFieldMapping
     * @param ClassMetadata $class
     */
    public function testFieldMappings($class)
    {
        $this->assertCount(12, $class->fieldMappings);
        $this->assertTrue(isset($class->fieldMappings['string']));
        $this->assertEquals('string', $class->fieldMappings['string']['type']);
        $this->assertTrue(isset($class->fieldMappings['binary']));
        $this->assertEquals('binary', $class->fieldMappings['binary']['type']);
        $this->assertTrue(isset($class->fieldMappings['long']));
        $this->assertEquals('long', $class->fieldMappings['long']['type']);
        $this->assertTrue(isset($class->fieldMappings['int']));
        $this->assertEquals('long', $class->fieldMappings['int']['type']);
        $this->assertTrue(isset($class->fieldMappings['decimal']));
        $this->assertEquals('decimal', $class->fieldMappings['decimal']['type']);
        $this->assertTrue(isset($class->fieldMappings['double']));
        $this->assertEquals('double', $class->fieldMappings['double']['type']);
        $this->assertTrue(isset($class->fieldMappings['float']));
        $this->assertEquals('double', $class->fieldMappings['float']['type']);
        $this->assertTrue(isset($class->fieldMappings['date']));
        $this->assertEquals('date', $class->fieldMappings['date']['type']);
        $this->assertTrue(isset($class->fieldMappings['boolean']));
        $this->assertEquals('boolean', $class->fieldMappings['boolean']['type']);
        $this->assertTrue(isset($class->fieldMappings['name']));
        $this->assertEquals('name', $class->fieldMappings['name']['type']);
        $this->assertTrue(isset($class->fieldMappings['path']));
        $this->assertEquals('path', $class->fieldMappings['path']['type']);
        $this->assertTrue(isset($class->fieldMappings['uri']));
        $this->assertEquals('uri', $class->fieldMappings['uri']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testIdentifier($class)
    {
        $this->assertEquals('id', $class->identifier);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testStringFieldMappings($class)
    {
        $this->assertEquals('string', $class->fieldMappings['string']['name']);
        $this->assertEquals('string', $class->fieldMappings['string']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testBinaryFieldMappings($class)
    {
        $this->assertEquals('binary', $class->fieldMappings['binary']['name']);
        $this->assertEquals('binary', $class->fieldMappings['binary']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testLongFieldMappings($class)
    {
        $this->assertEquals('long', $class->fieldMappings['long']['name']);
        $this->assertEquals('long', $class->fieldMappings['long']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testIntFieldMappings($class)
    {
        $this->assertEquals('int', $class->fieldMappings['int']['name']);
        $this->assertEquals('long', $class->fieldMappings['int']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testDecimalFieldMappings($class)
    {
        $this->assertEquals('decimal', $class->fieldMappings['decimal']['name']);
        $this->assertEquals('decimal', $class->fieldMappings['decimal']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testDoubleFieldMappings($class)
    {
        $this->assertEquals('double', $class->fieldMappings['double']['name']);
        $this->assertEquals('double', $class->fieldMappings['double']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testFloatFieldMappings($class)
    {
        $this->assertEquals('float', $class->fieldMappings['float']['name']);
        $this->assertEquals('double', $class->fieldMappings['float']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testDateFieldMappings($class)
    {
        $this->assertEquals('date', $class->fieldMappings['date']['name']);
        $this->assertEquals('date', $class->fieldMappings['date']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testBooleanFieldMappings($class)
    {
        $this->assertEquals('boolean', $class->fieldMappings['boolean']['name']);
        $this->assertEquals('boolean', $class->fieldMappings['boolean']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testNameFieldMappings($class)
    {
        $this->assertEquals('name', $class->fieldMappings['name']['name']);
        $this->assertEquals('name', $class->fieldMappings['name']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testPathFieldMappings($class)
    {
        $this->assertEquals('path', $class->fieldMappings['path']['name']);
        $this->assertEquals('path', $class->fieldMappings['path']['type']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testUriFieldMappings($class)
    {
        $this->assertEquals('uri', $class->fieldMappings['uri']['name']);
        $this->assertEquals('uri', $class->fieldMappings['uri']['type']);

        return $class;
    }

    public function testLoadNodenameMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\NodenameMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadNodenameMapping
     * @param ClassMetadata $class
     */
    public function testNodenameMapping($class)
    {
        $this->assertTrue(isset($class->nodename));
        $this->assertEquals('namefield', $class->nodename);
    }

    public function testLoadParentDocumentMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\ParentDocumentMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadParentDocumentMapping
     * @param ClassMetadata $class
     */
    public function testParentDocumentMapping($class)
    {
        $this->assertTrue(isset($class->parentMapping));
        $this->assertEquals('parent', $class->parentMapping);
    }

    public function testParentWithPrivatePropertyMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\ParentWithPrivatePropertyObject';
        $class = $this->loadMetadataForClassname($className);
        $this->assertEquals('foo', $class->fieldMappings['foo']['name']);
        $this->assertEquals('string', $class->fieldMappings['foo']['type']);

        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\ParentPrivatePropertyMappingObject';
        $class = $this->loadMetadataForClassname($className);

        $this->assertTrue(isset($class->identifier));
        $this->assertEmpty($class->fieldMappings);

        $session = $this->getMock('PHPCR\SessionInterface');
        $dm = \Doctrine\ODM\PHPCR\DocumentManager::create($session);
        $dm->getConfiguration()->setMetadataDriverImpl($this->loadDriver());
        $cmf = new ClassMetadataFactory($dm);
        $class = $cmf->getMetadataFor($className);

        $this->assertEquals('foo', $class->fieldMappings['foo']['name']);
        $this->assertEquals('string', $class->fieldMappings['foo']['type']);
    }

    public function testLoadChildMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\ChildMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadChildMapping
     * @param ClassMetadata $class
     */
    public function testChildMapping($class)
    {
        $this->assertTrue(isset($class->childMappings));
        $this->assertCount(2, $class->childMappings);
        $this->assertTrue(isset($class->childMappings['child1']));
        $this->assertEquals('first', $class->childMappings['child1']['name']);
        $this->assertTrue(isset($class->childMappings['child2']));
        $this->assertEquals('second', $class->childMappings['child2']['name']);
    }

    public function testLoadChildrenMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\ChildrenMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadChildrenMapping
     * @param ClassMetadata $class
     */
    public function testChildrenMapping($class)
    {
        $this->assertTrue(isset($class->childrenMappings));
        $this->assertCount(2, $class->childrenMappings);
        $this->assertTrue(isset($class->childrenMappings['all']));
        $this->assertFalse(isset($class->childrenMappings['all']['filter']));
        $this->assertTrue(isset($class->childrenMappings['some']));
        $this->assertEquals('*some*', $class->childrenMappings['some']['filter']);
        $this->assertEquals(2, $class->childrenMappings['some']['fetchDepth']);
    }

    public function testLoadRepositoryMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\RepositoryMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadRepositoryMapping
     * @param ClassMetadata $class
     */
    public function testRepositoryMapping($class)
    {
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\DocumentRepository', $class->customRepositoryClassName);
        $this->assertTrue($class->isIdGeneratorRepository());
    }

    public function testLoadVersionableMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\VersionableMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadVersionableMapping
     * @param ClassMetadata $class
     */
    public function testVersionableMapping($class)
    {
        $this->assertEquals('simple', $class->versionable);
        $this->assertEquals('versionName', $class->versionNameField);
        $this->assertEquals('versionCreated', $class->versionCreatedField);
    }

    public function testLoadReferenceableMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\ReferenceableMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadReferenceableMapping
     * @param ClassMetadata $class
     */
    public function testReferenceableMapping($class)
    {
        $this->assertTrue($class->referenceable);
    }

    public function testLoadNodeTypeMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\NodeTypeMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadNodeTypeMapping
     * @param ClassMetadata $class
     */
    public function testNodeTypeMapping($class)
    {
        $this->assertEquals('nt:test', $class->nodeType);
    }

    public function testLoadMappedSuperclassTypeMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\MappedSuperclassMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadMappedSuperclassTypeMapping
     * @param ClassMetadata $class
     */
    public function testMappedSuperclassTypeMapping($class)
    {
        $this->assertTrue($class->isMappedSuperclass);
    }

    public function testLoadNodeMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\NodeMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadNodeMapping
     * @param ClassMetadata $class
     */
    public function testNodeMapping($class)
    {
        $this->assertEquals('node', $class->node);
    }

    public function testLoadReferenceOneMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\ReferenceOneMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadReferenceOneMapping
     * @param ClassMetadata $class
     */
    public function testReferenceOneMapping($class)
    {
        $this->assertEquals(2, count($class->associationsMappings));
        $this->assertTrue(isset($class->associationsMappings['referenceOneWeak']));
        $this->assertCount(2, $class->getAssociationNames());
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\myDocument', $class->getAssociationTargetClass('referenceOneWeak'));
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\myDocument', $class->getAssociationTargetClass('referenceOneHard'));

        $referenceOneWeak = $class->associationsMappings['referenceOneWeak'];
        $this->assertEquals('referenceOneWeak', $referenceOneWeak['fieldName']);
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\myDocument', $referenceOneWeak['targetDocument']);
        $this->assertEquals('weak', $referenceOneWeak['strategy']);
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\ReferenceOneMappingObject', $referenceOneWeak['sourceDocument']);
        $this->assertEquals(ClassMetadata::MANY_TO_ONE, $referenceOneWeak['type']);

        $referenceOneHard = $class->associationsMappings['referenceOneHard'];
        $this->assertEquals('referenceOneHard', $referenceOneHard['fieldName']);
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\myDocument', $referenceOneHard['targetDocument']);
        $this->assertEquals('hard', $referenceOneHard['strategy']);
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\ReferenceOneMappingObject', $referenceOneHard['sourceDocument']);
        $this->assertEquals(ClassMetadata::MANY_TO_ONE, $referenceOneHard['type']);
    }

    public function testLoadReferenceManyMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\ReferenceManyMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadReferenceManyMapping
     * @param ClassMetadata $class
     */
    public function testReferenceManyMapping($class)
    {
        $this->assertEquals(2, count($class->associationsMappings));
        $this->assertTrue(isset($class->associationsMappings['referenceManyWeak']));
        $this->assertCount(2, $class->getAssociationNames());

        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\myDocument', $class->getAssociationTargetClass('referenceManyWeak'));
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\myDocument', $class->getAssociationTargetClass('referenceManyHard'));

        $referenceManyWeak = $class->associationsMappings['referenceManyWeak'];
        $this->assertEquals('referenceManyWeak', $referenceManyWeak['fieldName']);
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\myDocument', $referenceManyWeak['targetDocument']);
        $this->assertEquals('weak', $referenceManyWeak['strategy']);
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\ReferenceManyMappingObject', $referenceManyWeak['sourceDocument']);
        $this->assertEquals(ClassMetadata::MANY_TO_MANY, $referenceManyWeak['type']);

        $referenceManyHard = $class->associationsMappings['referenceManyHard'];
        $this->assertEquals('referenceManyHard', $referenceManyHard['fieldName']);
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\myDocument', $referenceManyHard['targetDocument']);
        $this->assertEquals('hard', $referenceManyHard['strategy']);
        $this->assertEquals('Doctrine\Tests\ODM\PHPCR\Mapping\Model\ReferenceManyMappingObject', $referenceManyHard['sourceDocument']);
        $this->assertEquals(ClassMetadata::MANY_TO_MANY, $referenceManyHard['type']);
    }

    public function testLoadReferrersMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\ReferrersMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadReferrersMapping
     * @param ClassMetadata $class
     */
    public function testReferrersMapping($class)
    {
        $all = $class->referrersMappings['allReferrers'];
        $this->assertEquals('allReferrers', $all['fieldName']);
        $this->assertEquals('allReferrers', $all['name']);
        $this->assertEmpty($all['filter']);
        $this->assertNull($all['referenceType']);

        $filtered = $class->referrersMappings['filteredReferrers'];
        $this->assertEquals('filteredReferrers', $filtered['fieldName']);
        $this->assertEquals('filteredReferrers', $filtered['name']);
        $this->assertEquals('test_filter', $filtered['filter']);
        $this->assertEmpty($filtered['referenceType']);

        $hard = $class->referrersMappings['hardReferrers'];
        $this->assertEquals('hardReferrers', $hard['fieldName']);
        $this->assertEquals('hardReferrers', $hard['name']);
        $this->assertEmpty($hard['filter']);
        $this->assertEquals('hard', $hard['referenceType']);

        $weak = $class->referrersMappings['weakReferrers'];
        $this->assertEquals('weakReferrers', $weak['fieldName']);
        $this->assertEquals('weakReferrers', $weak['name']);
        $this->assertEmpty($weak['filter']);
        $this->assertEquals('weak', $weak['referenceType']);
    }

    public function testLoadTranslatorMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\TranslatorMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadTranslatorMapping
     * @param ClassMetadata $class
     */
    public function testTranslatorMapping($class)
    {
        $this->assertEquals('attribute', $class->translator);
        $this->assertEquals('doclocale', $class->localeMapping);
        $this->assertEquals(2, count($class->translatableFields));
        $this->assertContains('topic', $class->translatableFields);
        $this->assertContains('image', $class->translatableFields);
    }

    public function testLoadLifecycleCallbackMapping()
    {
        $className = 'Doctrine\Tests\ODM\PHPCR\Mapping\Model\LifecycleCallbackMappingObject';

        return $this->loadMetadataForClassname($className);
    }

    /**
     * @depends testLoadLifecycleCallbackMapping
     * @param ClassMetadata $class
     */
    public function testLifecycleCallbackMapping($class)
    {
        $this->assertEquals(7, count($class->lifecycleCallbacks));
        $this->assertEquals('preRemoveFunc', $class->lifecycleCallbacks['preRemove'][0]);
        $this->assertEquals('postRemoveFunc', $class->lifecycleCallbacks['postRemove'][0]);
        $this->assertEquals('prePersistFunc', $class->lifecycleCallbacks['prePersist'][0]);
        $this->assertEquals('postPersistFunc', $class->lifecycleCallbacks['postPersist'][0]);
        $this->assertEquals('preUpdateFunc', $class->lifecycleCallbacks['preUpdate'][0]);
        $this->assertEquals('postUpdateFunc', $class->lifecycleCallbacks['postUpdate'][0]);
        $this->assertEquals('postLoadFunc', $class->lifecycleCallbacks['postLoad'][0]);
    }
}
