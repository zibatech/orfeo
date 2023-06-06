<?php

require_once 'zidoc/Zidoc.php';
require_once 'processConfig.php';

$signature = $signature_zidoc;
$api_uri = $api_zidoc;

beforeEach(function () use ($signature, $api_uri) {
    $this->zidoc = new Zidoc($signature, $api_uri);
});

test('instancia zidoc', function () {
    expect(get_class($this->zidoc))->toBe('Zidoc');
});

test('servicio activo', function() {
	expect($this->zidoc->verificarServicio())->toBeInt(200);
});

test('consulta expediente', function() {
    expect($this->zidoc->buscar('202115009010001E'))->toBeArray();
});

test('consulta documentos', function() {
    expect($this->zidoc->documentos('1'))->toBeArray();
});
