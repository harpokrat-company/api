<?php


namespace App\Tests\functional;


use PHPUnit\Framework\Assert;

trait JsonApiResourceHelperTrait
{
    private function assertIsUuidV4(string $id)
    {
        Assert::assertStringMatchesFormat('%x-%x-%x-%x-%x', $id);
    }

    private function assertContentIsWellFormedJsonApiResource(string $content, string $type = null)
    {
        Assert::assertJson($content);
        $arrayContent = json_decode($content, true);
        Assert::assertIsArray($arrayContent);
        Assert::assertArrayHasKey('data', $arrayContent);
        $data = $arrayContent['data'];
        Assert::assertArrayHasKey('type', $data);
        if (!is_null($type))
            Assert::assertSame($type, $data['type']);
        else
            Assert::assertIsString($data['type']);
        Assert::assertArrayHasKey('id', $data);
        $this->assertIsUuidV4($data['id']);
        return $data;
    }
}
