<?php

namespace Tests\ExampleTest;

use Test\TestCase;

/**
 * EasyRdf
 *
 * LICENSE
 *
 * Copyright (c) 2009-2014 Nicholas J Humfrey.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. The name of the author 'Nicholas J Humfrey" may be used to endorse or
 *    promote products derived from this software without specific prior
 *    written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright  Copyright (c) 2009-2014 Nicholas J Humfrey
 * @license    https://www.opensource.org/licenses/bsd-license.php
 */
class SerialiseTest extends TestCase
{
    public function testNtriples()
    {
        $output = executeExample(
            'serialise.php',
            ['format' => 'ntriples']
        );
        $this->assertStringContainsString('<title>EasyRdf Serialiser Example</title>', $output);
        $this->assertStringContainsString(
            '&lt;http://www.example.com/joe#me&gt; '.
            '&lt;http://www.w3.org/1999/02/22-rdf-syntax-ns#type&gt; '.
            '&lt;http://xmlns.com/foaf/0.1/Person&gt; .',
            $output
        );
    }

    public function testRdfXml()
    {
        $output = executeExample(
            'serialise.php',
            ['format' => 'rdfxml']
        );
        $this->assertStringContainsString('<title>EasyRdf Serialiser Example</title>', $output);
        $this->assertStringContainsString(
            '&lt;foaf:Person rdf:about=&quot;http://www.example.com/joe#me&quot;&gt;',
            $output
        );
    }

    public function testPhp()
    {
        $output = executeExample(
            'serialise.php',
            ['format' => 'php']
        );
        $this->assertStringContainsString('<title>EasyRdf Serialiser Example</title>', $output);

        if (version_compare(\PHP_VERSION, '8.1.0', '>=')) {
            $this->assertStringContainsString('value&#039; =&gt; &#039;http://xmlns.com/foaf/0.1/Person', $output);
        } else {
            $this->assertStringContainsString("'value' =&gt; 'http://xmlns.com/foaf/0.1/Person',", $output);
        }
    }
}
