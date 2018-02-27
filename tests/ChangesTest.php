<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Stream\Tests;

use Covex\Stream\Changes;
use Covex\Stream\File\Entity;
use PHPUnit\Framework\TestCase;

class ChangesTest extends TestCase
{
    public function testInterface(): void
    {
        $changes = new Changes();

        $this->assertFalse($changes->exists('qqq/www/eee'));
        $this->assertNull($changes->get('qqq/www/eee'));
        $this->assertFalse($changes->delete('qqq/www/eee'));
        $this->assertEquals(0, $changes->count());
        $this->assertEquals([], $changes->children());

        $entity = Entity::newInstance(__FILE__);

        $changes->add('qqq/www/eee', $entity);

        $sublists1 = $changes->sublists();
        $this->assertEquals(1, count($sublists1['qqq']));
        $this->assertTrue(isset($sublists1['qqq']));
        $this->assertTrue($sublists1['qqq'] instanceof Changes);

        $this->assertTrue($changes->exists('qqq/www/eee'));
        $this->assertEquals($entity, $changes->get('qqq/www/eee'));
        $this->assertEquals(1, $changes->count());

        $expectedChildren = [
            'qqq/www/eee' => $entity,
        ];
        $this->assertEquals($expectedChildren, $changes->children());
        $this->assertEquals($expectedChildren, $changes->children('qqq'));
        $this->assertEquals($expectedChildren, $changes->children('qqq/www'));
        $this->assertEquals([], $changes->children('qqq/www/eee'));

        $this->assertEquals([], $changes->own());
        $this->assertEquals([], $changes->own('qqq'));
        $this->assertEquals(['qqq/www/eee' => $entity], $changes->own('qqq/www'));
        $this->assertEquals([], $changes->own('qqq/www/eee'));

        $this->assertTrue($changes->delete('qqq/www/eee'));

        $this->assertFalse($changes->exists('qqq/www/eee'));
        $this->assertNull($changes->get('qqq/www/eee'));
        $this->assertFalse($changes->delete('qqq/www/eee'));
        $this->assertEquals(0, $changes->count());
        $this->assertEquals([], $changes->children());
    }
}
