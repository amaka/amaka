<?php

namespace Officine\Amaka\AmakaScript;

/**
 * This class can be used to build a graph representation of any given
 * amaka script useful for determining if a circular dependency between
 * invocables exist in the script.
 *
 * @SuppressWarnings(PHPMD)
 */
class Dag extends AbstractDirectedGraph
{
    private $loaded = false;
    private $cycles = array();
    private $table;

    public function __construct(SymbolTable $table)
    {
        $this->table = $table;
        // Note: we call load here for backward compatibility reasons.
        // Remove this line and refactor test cases and classes before
        // moving on to other stuff.
        $this->load();
    }

    public function getCycles()
    {
        return $this->cycles;
    }

    private function load()
    {
        if ($this->loaded) {
            return;
        }
        foreach ($this->table->getAllSymbols() as $sName => $sRequisites) {
            $this->addNodes([$sName => $sRequisites]);
        }
        $this->loaded = true;
    }

    public function loadEdges(array $symbolsNames)
    {
        return $this->table->getAllSymbols();
    }

    public function isValid($wEntry = null)
    {
        $this->loadGraph();

        // Detect cycles in the entire graph
        if (! $wEntry) {
            foreach ($this->table->getAllSymbols() as $sName => $sRequisites) {
                if (! $this->isValid($sName)) {
                    return false;
                }
            }
            return true;
        }

        if ($cycles = $this->detectCycles($wEntry)) {
            $this->cycles[] = $cycles;
            return false;
        }

        return true;
    }

    public function getExceptionClass()
    {
        return new CycleDetectionException($this->cycles);
    }
}

/**
 * The following class is part of Facebook Libphutil (https://github.com/facebook/libphutil)
 * it has been embedded to avoid adding the entire library as required dependency for Amaka.
 *
 */

/**
 * Models a directed graph in a generic way that works well with graphs stored
 * in a database, and allows you to perform operations like cycle detection.
 *
 * To use this class, seed it with a set of edges (e.g., the new candidate
 * edges the user is trying to create) using @{method:addNodes}, then
 * call @{method:loadGraph} to construct the graph.
 *
 *   $detector = new ExamplePhabricatorGraphCycleDetector();
 *   $detector->addNodes(
 *     array(
 *       $object->getPHID() => $object->getChildPHIDs(),
 *     ));
 *   $detector->loadGraph();
 *
 * Now you can query the graph, e.g. by detecting cycles:
 *
 *   $cycle = $detector->detectCycles($object->getPHID());
 *
 * If ##$cycle## is empty, no graph cycle is reachable from the node. If it
 * is nonempty, it contains a list of nodes which form a graph cycle.
 *
 * NOTE: Nodes must be represented with scalars.
 *
 * @task build Graph Construction
 * @task cycle Cycle Detection
 * @task explore Graph Exploration
 *
 * @group util
 * @stable
 * @SuppressWarnings(PHPMD)
 * @codeCoverageIgnore
 */
abstract class AbstractDirectedGraph {


  private $knownNodes   = array();
  private $graphLoaded  = false;


/* -(  Graph Construction  )------------------------------------------------- */


  /**
   * Load the edges for a list of nodes. You must override this method. You
   * will be passed a list of nodes, and should return a dictionary mapping
   * each node to the list of nodes that can be reached by following its the
   * edges which originate at it: for example, the child nodes of an object
   * which has a parent-child relationship to other objects.
   *
   * The intent of this method is to allow you to issue a single query per
   * graph level for graphs which are stored as edge tables in the database.
   * Generally, you will load all the objects which correspond to the list of
   * nodes, and then return a map from each of their IDs to all their children.
   *
   * NOTE: You must return an entry for every node you are passed, even if it
   * is invalid or can not be loaded. Either return an empty array (if this is
   * acceptable for your application) or throw an exception if you can't satisfy
   * this requirement.
   *
   * @param   list  A list of nodes.
   * @return  dict  A map of nodes to the nodes reachable along their edges.
   *                There must be an entry for each node you were provided.
   * @task build
   */
  abstract protected function loadEdges(array $nodes);


  /**
   * Seed the graph with known nodes. Often, you will provide the candidate
   * edges that a user is trying to create here, or the initial set of edges
   * you know about.
   *
   * @param   dict  A map of nodes to the nodes reachable along their edges.
   * @return  this
   * @task build
   */
  final public function addNodes(array $nodes) {
    if ($this->graphLoaded) {
      throw new Exception(
        "Call addNodes() before calling loadGraph(). You can not add more ".
        "nodes once you have loaded the graph.");
    }

    $this->knownNodes += $nodes;
    return $this;
  }


  /**
   * Load the graph, building it out so operations can be performed on it. This
   * constructs the graph level-by-level, calling @{method:loadEdges} to
   * expand the graph at each stage until it is complete.
   *
   * @return this
   * @task build
   */
  final public function loadGraph() {

    $new_nodes = $this->knownNodes;
    while (true) {
      $load = array();
      foreach ($new_nodes as $node => $edges) {
        foreach ($edges as $edge) {
          if (!isset($this->knownNodes[$edge])) {
            $load[$edge] = true;
          }
        }
      }

      if (empty($load)) {
        break;
      }

      $load = array_keys($load);

      $new_nodes = $this->loadEdges($load);
      foreach ($load as $node) {
        if (!isset($new_nodes[$node]) || !is_array($new_nodes[$node])) {
          throw new Exception(
            "loadEdges() must return an edge list array for each provided ".
            "node, or the cycle detection algorithm may not terminate.");
        }
      }

      $this->addNodes($new_nodes);
    }

    $this->graphLoaded = true;
    return $this;
  }


/* -(  Cycle Detection  )---------------------------------------------------- */


  /**
   * Detect if there are any cycles reachable from a given node.
   *
   * If cycles are reachable, it returns a list of nodes which create a cycle.
   * Note that this list may include nodes which aren't actually part of the
   * cycle, but lie on the graph between the specified node and the cycle.
   * For example, it might return something like this (when passed "A"):
   *
   *    A, B, C, D, E, C
   *
   * This means you can walk from A to B to C to D to E and then back to C,
   * which forms a cycle. A and B are included even though they are not part
   * of the cycle. When presenting information about graph cycles to users,
   * including these nodes is generally useful. This also shouldn't ever happen
   * if you've vetted prior edges before writing them, because it means there
   * is a preexisting cycle in the graph.
   *
   * NOTE: This only detects cycles reachable from a node. It does not detect
   * cycles in the entire graph.
   *
   * @param   scalar    The node to walk from, looking for graph cycles.
   * @return  list|null Returns null if no cycles are reachable from the node,
   *                    or a list of nodes that form a cycle.
   * @task cycle
   */
  final public function detectCycles($node) {
    if (!$this->graphLoaded) {
      throw new \Exception(
        "Call loadGraph() to build the graph out before calling ".
        "detectCycles().");
    }
    if (!isset($this->knownNodes[$node])) {
      throw new \Exception(
        "The node '{$node}' is not known. Call addNodes() to seed the graph ".
        "with nodes.");
    }

    $visited = array();
    return $this->performCycleDetection($node, $visited);
  }


  /**
   * Internal cycle detection implementation. Recursively walks the graph,
   * keeping track of where it's been, and returns the first cycle it finds.
   *
   * @param   scalar      The node to walk from.
   * @param   list        Previously visited nodes.
   * @return  null|list   Null if no cycles are found, or a list of nodes
   *                      which cycle.
   * @task cycle
   */
  final private function performCycleDetection($node, array $visited) {
    $visited[$node] = true;
    foreach ($this->knownNodes[$node] as $edge) {
      if (isset($visited[$edge])) {
        $result = array_keys($visited);
        $result[] = $edge;
        return $result;
      }
      $result = $this->performCycleDetection($edge, $visited);
      if ($result) {
        return $result;
      }
    }
    return null;
  }

}
