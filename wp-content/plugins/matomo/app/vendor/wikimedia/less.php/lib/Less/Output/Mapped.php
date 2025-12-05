<?php

namespace {
    /**
     * Parser output with source map
     *
     * @private
     */
    class Less_Output_Mapped extends \Less_Output
    {
        /**
         * The source map generator
         *
         * @var Less_SourceMap_Generator
         */
        protected $generator;
        /**
         * Current line
         *
         * @var int
         */
        protected $lineNumber = 0;
        /**
         * Current column
         *
         * @var int
         */
        protected $column = 0;
        /**
         * Array of contents map (file and its content)
         *
         * @var array
         */
        protected $contentsMap = [];
        /**
         * Constructor
         *
         * @param array $contentsMap Array of filename to contents map
         * @param Less_SourceMap_Generator $generator
         */
        public function __construct(array $contentsMap, $generator)
        {
            $this->contentsMap = $contentsMap;
            $this->generator = $generator;
        }
        /**
         * Adds a chunk to the stack
         * The $index for less.php may be different from less.js since less.php does not chunkify inputs
         *
         * @param string $chunk
         * @param array|null $fileInfo
         * @param int $index
         * @param mixed $mapLines
         */
        public function add($chunk, $fileInfo = null, $index = 0, $mapLines = null)
        {
            // ignore adding empty strings
            if ($chunk === '') {
                return;
            }
            $sourceLines = [];
            $sourceColumns = ' ';
            if ($fileInfo) {
                $url = $fileInfo['currentUri'];
                if (isset($this->contentsMap[$url])) {
                    $inputSource = \substr($this->contentsMap[$url], 0, $index);
                    $sourceLines = \explode("\n", $inputSource);
                    $sourceColumns = \end($sourceLines);
                } else {
                    throw new \Exception('Filename ' . $url . ' not in contentsMap');
                }
            }
            $lines = \explode("\n", $chunk);
            $columns = \end($lines);
            if ($fileInfo) {
                if (!$mapLines) {
                    $this->generator->addMapping(
                        $this->lineNumber + 1,
                        // generated_line
                        $this->column,
                        // generated_column
                        \count($sourceLines),
                        // original_line
                        \strlen($sourceColumns),
                        // original_column
                        $fileInfo
                    );
                } else {
                    for ($i = 0, $count = \count($lines); $i < $count; $i++) {
                        $this->generator->addMapping(
                            $this->lineNumber + $i + 1,
                            // generated_line
                            $i === 0 ? $this->column : 0,
                            // generated_column
                            \count($sourceLines) + $i,
                            // original_line
                            $i === 0 ? \strlen($sourceColumns) : 0,
                            // original_column
                            $fileInfo
                        );
                    }
                }
            }
            if (\count($lines) === 1) {
                $this->column += \strlen($columns);
            } else {
                $this->lineNumber += \count($lines) - 1;
                $this->column = \strlen($columns);
            }
            // add only chunk
            parent::add($chunk);
        }
    }
}
