<?php

class CM_File_FilesystemTest extends CMTest_TestCase {

    protected function tearDown() {
        CMTest_TH::clearEnv();
    }

    public function testDeleteByPrefix() {
        $filesystem = new CM_File_Filesystem(new CM_File_Filesystem_Adapter_Local());
        $dirTmp = CM_Bootloader::getInstance()->getDirTmp();

        $pathList = array(
            'foo/foobar/bar',
            'foo/bar2',
            'foo/bar',
        );
        /** @var CM_File[] $fileList */
        $fileList = array();
        foreach ($pathList as $path) {
            $file = new CM_File($dirTmp . $path, $filesystem);
            $file->ensureParentDirectory();
            $file->write('hello');
            $fileList[] = $file;
            $fileList[] = $file->getParentDirectory();
        }

        foreach ($fileList as $file) {
            $this->assertTrue($file->getExists());
        }

        $filesystem->deleteByPrefix($dirTmp);

        foreach ($fileList as $file) {
            $this->assertFalse($file->getExists());
        }
        $this->assertTrue((new CM_File($dirTmp))->getExists());
    }

    public function testNormalizePath() {
        $this->assertSame('/foo', CM_File_Filesystem::normalizePath('/foo'));
        $this->assertSame('/foo', CM_File_Filesystem::normalizePath('/foo/'));
        $this->assertSame('/', CM_File_Filesystem::normalizePath('/'));
        $this->assertSame('/', CM_File_Filesystem::normalizePath(''));
        $this->assertSame('/', CM_File_Filesystem::normalizePath('//'));
        $this->assertSame('/foo/mega', CM_File_Filesystem::normalizePath('/foo/bar/../mega'));
        $this->assertSame('/', CM_File_Filesystem::normalizePath('/../..'));
        $this->assertSame('/foo/bar', CM_File_Filesystem::normalizePath('/foo/./bar'));
        $this->assertSame('/foo/bar', CM_File_Filesystem::normalizePath('/foo/./bar///'));
        $this->assertSame('/foo', CM_File_Filesystem::normalizePath('../foo'));
        $this->assertSame('/foo', CM_File_Filesystem::normalizePath('foo'));
    }

    public function testSecondary() {
        $dirTmp = CM_Bootloader::getInstance()->getDirTmp();
        $filesystem = new CM_File_Filesystem(new CM_File_Filesystem_Adapter_Local($dirTmp));
        $filesystemSecondary = $this->getMockBuilder('CM_File_Filesystem')->disableOriginalConstructor()->setMethods(array(
            'write',
            'read',
            'rename',
            'append',
            'delete',
            'ensureDirectory',
            'deleteByPrefix',
        ))->getMock();
        $filesystemSecondary->expects($this->once())->method('write')->with('/foo', 'hello');
        $filesystemSecondary->expects($this->never())->method('read');
        $filesystemSecondary->expects($this->once())->method('append')->with('/foo', 'world');
        $filesystemSecondary->expects($this->once())->method('rename')->with('/foo', '/bar');
        $filesystemSecondary->expects($this->once())->method('delete')->with('/bar');
        $filesystemSecondary->expects($this->once())->method('ensureDirectory')->with('/my-dir');
        $filesystemSecondary->expects($this->once())->method('deleteByPrefix')->with('/my-dir');
        /** @var CM_File_Filesystem $filesystemSecondary */
        $filesystem->addSecondary($filesystemSecondary);

        $filesystem->write('/foo', 'hello');
        $filesystem->read('/foo');
        $filesystem->append('/foo', 'world');
        $filesystem->rename('/foo', '/bar');
        $filesystem->delete('/bar');
        $filesystem->ensureDirectory('/my-dir');
        $filesystem->deleteByPrefix('/my-dir');
    }
}
