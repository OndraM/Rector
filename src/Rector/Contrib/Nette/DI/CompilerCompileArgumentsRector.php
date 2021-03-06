<?php declare(strict_types=1);

namespace Rector\Rector\Contrib\Nette\DI;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use Rector\NodeAnalyzer\MethodCallAnalyzer;
use Rector\Rector\AbstractRector;

/**
 * Nette\DI\Compiler::compile arguments are deprecated, use Compiler::addConfig() and Compiler::setClassName().
 *
 * From:
 * - $compiler->compile($config, $className);
 *
 * To:
 * - $compiler->compile();
 * - $compiler->addConfig($config);
 * - $compiler->setClassName($className);
 */
final class CompilerCompileArgumentsRector extends AbstractRector
{
    /**
     * @var MethodCallAnalyzer
     */
    private $methodCallAnalyzer;

    public function __construct(MethodCallAnalyzer $methodCallAnalyzer)
    {
        $this->methodCallAnalyzer = $methodCallAnalyzer;
    }

    public function isCandidate(Node $node): bool
    {
        if (! $this->methodCallAnalyzer->isTypeAndMethod($node, 'Nette\DI\Compiler', 'compile')) {
            return false;
        }

        /** @var MethodCall $node */
        return count($node->args) >= 1;
    }

    /**
     * @param MethodCall $methodCallNode
     */
    public function refactor(Node $methodCallNode): ?Node
    {
        $oldArguments = $methodCallNode->args;

        $addConfigMethodCallNode = $this->cloneMethodWithNameAndArgument(
            $methodCallNode,
            'addConfig',
            $oldArguments[0]
        );
        $this->prependNodeAfterNode($addConfigMethodCallNode, $methodCallNode);

        if (isset($oldArguments[1])) {
            $setClassNameMethodCallNode = $this->cloneMethodWithNameAndArgument(
                $methodCallNode,
                'setClassName',
                $oldArguments[1]
            );
            $this->prependNodeAfterNode($setClassNameMethodCallNode, $methodCallNode);
        }

        $methodCallNode->args = [];

        return $methodCallNode;
    }

    private function cloneMethodWithNameAndArgument(
        MethodCall $methodCallNode,
        string $method,
        Arg $argNode
    ): MethodCall {
        $addConfigMethodCallNode = clone $methodCallNode;
        $addConfigMethodCallNode->name = new Identifier($method);
        $addConfigMethodCallNode->args = [$argNode];

        return $addConfigMethodCallNode;
    }
}
