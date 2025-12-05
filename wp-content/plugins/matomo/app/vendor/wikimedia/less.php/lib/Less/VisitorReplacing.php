<?php

namespace {
    /**
     * @private
     */
    class Less_VisitorReplacing extends \Less_Visitor
    {
        public function visitObj($node)
        {
            $funcName = 'visit' . $node->type;
            if (isset($this->_visitFnCache[$funcName])) {
                $visitDeeper = \true;
                $node = $this->{$funcName}($node, $visitDeeper);
                if ($node) {
                    if ($visitDeeper && \is_object($node)) {
                        $node->accept($this);
                    }
                    $funcName .= "Out";
                    if (isset($this->_visitFnCache[$funcName])) {
                        $this->{$funcName}($node);
                    }
                }
            } else {
                $node->accept($this);
            }
            return $node;
        }
        public function visitArray($nodes)
        {
            $newNodes = [];
            foreach ($nodes as $node) {
                $evald = $this->visitObj($node);
                if ($evald) {
                    if (\is_array($evald)) {
                        self::flatten($evald, $newNodes);
                    } else {
                        $newNodes[] = $evald;
                    }
                }
            }
            return $newNodes;
        }
        public function flatten($arr, &$out)
        {
            foreach ($arr as $item) {
                if (!\is_array($item)) {
                    $out[] = $item;
                    continue;
                }
                foreach ($item as $nestedItem) {
                    if (\is_array($nestedItem)) {
                        self::flatten($nestedItem, $out);
                    } else {
                        $out[] = $nestedItem;
                    }
                }
            }
            return $out;
        }
    }
}
