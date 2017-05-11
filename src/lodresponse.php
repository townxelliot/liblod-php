<?php

/* A LODResponse is used by a context to encapsulate an HTTP response
 * that can be processed into the model by a LOD context. LODResponses
 * instances are created, processed, and destroyed as part of LOD::fetch()
 * automatically.
 */

require_once(dirname(__FILE__) . '/lod.php');

class LODResponse
{
    // LOD instance
    public $context;

	public $status = 0;
	public $error = 0;
	public $errMsg = NULL;

    // URI requested
	public $target = NULL;

    // content type, e.g. 'text/turtle' or 'application/rdf+xml'
	public $type = NULL;

    // response body
	public $payload = NULL;

	public function __construct(LOD $context)
	{
        $this->context = $context;
	}
}
