<?php

namespace Fomo\Facades;

/**
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise bulk(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise clearScroll(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise closePointInTime(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise count(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise create(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise delete(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise deleteByQuery(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise deleteByQueryRethrottle(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise deleteScript(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise exists(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise existsSource(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise explain(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise fieldCaps(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise get(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getScript(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getScriptContext(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getScriptLanguages(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise getSource(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise index(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise info(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise knnSearch(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise mget(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise msearch(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise msearchTemplate(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise mtermvectors(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise openPointInTime(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise ping(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise putScript(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise rankEval(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise reindex(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise reindexRethrottle(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise renderSearchTemplate(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise scriptsPainlessExecute(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise scroll(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise search(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise searchMvt(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise searchShards(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise searchTemplate(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise termsEnum(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise termvectors(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise update(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise updateByQuery(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise updateByQueryRethrottle(array $params = [])
 */
class Elasticsearch extends Facade
{
    protected static function getMainClass(): string
    {
        return 'elasticsearch';
    }
}