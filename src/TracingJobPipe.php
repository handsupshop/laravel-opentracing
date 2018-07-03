<?php

namespace LaravelOpenTracing;

use OpenTracing\StartSpanOptions;
use OpenTracing\Tracer;

class TracingJobPipe
{
    /**
     * @var TracingService
     */
    private $service;
    /**
     * @var array
     */
    private $options;

    /**
     * TracingJobPipe constructor.
     * @param TracingService $service
     * @param array|StartSpanOptions $options
     */
    public function __construct(TracingService $service, $options = [])
    {
        $this->service = $service;
        $this->options = $options;
    }

    /**
     * @param object $job
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($job, \Closure $next)
    {
        $res = $this->service->trace(
            function () use ($next, $job) {
                return $next($job);
            },
            get_class($job),
            $this->options
        );
        app(Tracer::class)->flush();
        return $res;
    }
}