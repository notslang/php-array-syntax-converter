<?php
require_once 'vendor/autoload.php';

// Import the Pharborist classes
use Pharborist\Filter;
use Pharborist\Node;
use Pharborist\Parser;
use Pharborist\TokenNode;
use Pharborist\TopNode;

function processTree(TopNode $tree) {
  /**
   * Tracks if we made a change to the tree.
   * @var bool $modified
   */
  $modified = FALSE;
  /**
   * Loop over array nodes in the tree.
   * @var \Pharborist\ArrayNode $array
   */
  foreach ($tree->find(Filter::isInstanceOf('\Pharborist\ArrayNode')) as $array) {
    #print $array . "\n'" . trim($array->lastChild()) . "'\n\n\n";
    // Test if using old syntax.
    if ($array->firstChild()->getText() === 'array') {
      // Remove any hidden tokens between T_ARRAY and ( .
      $array->firstChild()->nextUntil(function (Node $node) {
        return $node instanceof TokenNode && $node->getType() === '(';
      })->remove();
      $array->firstChild()->remove(); // remove T_ARRAY token.
      $array->firstChild()->replaceWith(new TokenNode('[', '[')); // replace ( with [

      $currentNode = $array->lastChild();
      while(!($currentNode instanceof TokenNode && $currentNode->getType() === ')')){
        $currentNode = $currentNode->previous();
      }
      $currentNode->replaceWith(new TokenNode(']', ']')); // replace ) with ] .
      $modified = TRUE;
    }
  }
  return $modified;
}

/**
 * Process a drupal php file.
 */
function processFile($filename) {
  if (substr($filename, 0, strlen('./core/vendor/')) === './core/vendor/') {
    // Ignore vendor files
    return;
  }
  try {
    $tree = Parser::parseFile($filename);
    $modified = processTree($tree);
    if ($modified) {
      file_put_contents($filename, $tree->getText());
    }
  } catch (\Pharborist\ParserException $e) {
    die($filename . ': ' . $e->getMessage() . PHP_EOL);
  }
}

// Find drupal php files.
$extensions = array('php', 'inc', 'module', 'install', 'theme');
$directory = new \RecursiveDirectoryIterator('.');
$iterator = new \RecursiveIteratorIterator($directory);
$pattern = '/^.+\.(' . implode('|', $extensions) . ')$/i';
$regex = new \RegexIterator($iterator, $pattern, \RecursiveRegexIterator::GET_MATCH);
foreach ($regex as $name => $object) {
  processFile($name);
}
