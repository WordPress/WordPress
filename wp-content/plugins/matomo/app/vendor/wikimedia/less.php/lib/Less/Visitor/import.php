<?php

namespace {
    /*
    class Less_Visitor_import extends Less_VisitorReplacing{
    
    	public $_visitor;
    	public $_importer;
    	public $importCount;
    
    	function __construct( $evalEnv ){
    		$this->env = $evalEnv;
    		$this->importCount = 0;
    		parent::__construct();
    	}
    
    
    	function run( $root ){
    		$root = $this->visitObj($root);
    		$this->isFinished = true;
    
    		//if( $this->importCount === 0) {
    		//	$this->_finish();
    		//}
    	}
    
    	function visitImport($importNode, &$visitDeeper ){
    		$importVisitor = $this;
    		$inlineCSS = $importNode->options['inline'];
    
    		if( !$importNode->css || $inlineCSS ){
    			$evaldImportNode = $importNode->compileForImport($this->env);
    
    			if( $evaldImportNode && (!$evaldImportNode->css || $inlineCSS) ){
    				$importNode = $evaldImportNode;
    				$this->importCount++;
    				$env = clone $this->env;
    
    				if( (isset($importNode->options['multiple']) && $importNode->options['multiple']) ){
    					$env->importMultiple = true;
    				}
    
    				//get path & uri
    				$path_and_uri = null;
    				if( is_callable(Less_Parser::$options['import_callback']) ){
    					$path_and_uri = call_user_func(Less_Parser::$options['import_callback'],$importNode);
    				}
    
    				if( !$path_and_uri ){
    					$path_and_uri = $importNode->PathAndUri();
    				}
    
    				if( $path_and_uri ){
    					list($full_path, $uri) = $path_and_uri;
    				}else{
    					$full_path = $uri = $importNode->getPath();
    				}
    
    
    				//import once
    				if( $importNode->skip( $full_path, $env) ){
    					return array();
    				}
    
    				if( $importNode->options['inline'] ){
    					//todo needs to reference css file not import
    					//$contents = new Less_Tree_Anonymous($importNode->root, 0, array('filename'=>$importNode->importedFilename), true );
    
    					Less_Parser::AddParsedFile($full_path);
    					$contents = new Less_Tree_Anonymous( file_get_contents($full_path), 0, array(), true );
    
    					if( $importNode->features ){
    						return new Less_Tree_Media( array($contents), $importNode->features->value );
    					}
    
    					return array( $contents );
    				}
    
    
    				// css ?
    				if( $importNode->css ){
    					$features = ( $importNode->features ? $importNode->features->compile($env) : null );
    					return new Less_Tree_Import( $importNode->compilePath( $env), $features, $importNode->options, $this->index);
    				}
    
    				return $importNode->ParseImport( $full_path, $uri, $env );
    			}
    
    		}
    
    		$visitDeeper = false;
    		return $importNode;
    	}
    
    
    	function visitRule( $ruleNode, &$visitDeeper ){
    		$visitDeeper = false;
    		return $ruleNode;
    	}
    
    	function visitDirective($directiveNode, $visitArgs){
    		array_unshift($this->env->frames,$directiveNode);
    		return $directiveNode;
    	}
    
    	function visitDirectiveOut($directiveNode) {
    		array_shift($this->env->frames);
    	}
    
    	function visitMixinDefinition($mixinDefinitionNode, $visitArgs) {
    		array_unshift($this->env->frames,$mixinDefinitionNode);
    		return $mixinDefinitionNode;
    	}
    
    	function visitMixinDefinitionOut($mixinDefinitionNode) {
    		array_shift($this->env->frames);
    	}
    
    	function visitRuleset($rulesetNode, $visitArgs) {
    		array_unshift($this->env->frames,$rulesetNode);
    		return $rulesetNode;
    	}
    
    	function visitRulesetOut($rulesetNode) {
    		array_shift($this->env->frames);
    	}
    
    	function visitMedia($mediaNode, $visitArgs) {
    		array_unshift($this->env->frames, $mediaNode->ruleset);
    		return $mediaNode;
    	}
    
    	function visitMediaOut($mediaNode) {
    		array_shift($this->env->frames);
    	}
    
    }
    */
}
