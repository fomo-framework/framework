<?php

namespace Fomo\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Http\Promise\Promise;

/**
 * class Elasticsearch
 *
 * Strings methods
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise bulk(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise clearScroll(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise closePointInTime(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise count(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise create(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise delete(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise deleteByQuery(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise deleteByQueryRethrottle(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise deleteScript(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise exists(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise existsSource(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise explain(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise fieldCaps(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise get(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise getScript(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise getScriptContext(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise getScriptLanguages(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise getSource(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise index(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise info(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise knnSearch(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise mget(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise msearch(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise msearchTemplate(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise mtermvectors(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise openPointInTime(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise ping(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise putScript(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise rankEval(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise reindex(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise reindexRethrottle(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise renderSearchTemplate(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise scriptsPainlessExecute(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise scroll(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise search(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise searchMvt(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise searchShards(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise searchTemplate(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise termsEnum(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise termvectors(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise update(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise updateByQuery(array $params = [])
 * @method static \Elastic\Elasticsearch\Response\Elasticsearch|Promise updateByQueryRethrottle(array $params = [])
 */
class Elasticsearch
{
    protected static Client $instance;

    public static function setInstance(): void
    {
        $instance = (new ClientBuilder())->setHosts([config('elasticsearch.host') . ':' . config('elasticsearch.port')]);

        if (config('elasticsearch.username') != null && config('elasticsearch.password') != null){
            $instance->setBasicAuthentication(config('elasticsearch.username') , config('elasticsearch.password'));
        }

        self::$instance = $instance->build();
    }

    public static function getInstance(): Client
    {
        return self::$instance;
    }

    public static function __callStatic(string $method, array $arguments)
    {
        return self::$instance->$method(...$arguments);
    }
}