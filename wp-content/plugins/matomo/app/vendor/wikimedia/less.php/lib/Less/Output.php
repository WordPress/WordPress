<?php

namespace {
    /**
     * Parser output
     *
     * @private
     */
    class Less_Output
    {
        /**
         * Output holder
         *
         * @var string[]
         */
        protected $strs = [];
        /**
         * Adds a chunk to the stack
         *
         * @param string $chunk The chunk to output
         * @param array|null $fileInfo The file information
         * @param int $index The index
         * @param mixed $mapLines
         */
        public function add($chunk, $fileInfo = null, $index = 0, $mapLines = null)
        {
            $this->strs[] = $chunk;
        }
        /**
         * Is the output empty?
         *
         * @return bool
         */
        public function isEmpty()
        {
            return \count($this->strs) === 0;
        }
        /**
         * Converts the output to string
         *
         * @return string
         */
        public function toString()
        {
            return \implode('', $this->strs);
        }
    }
}
