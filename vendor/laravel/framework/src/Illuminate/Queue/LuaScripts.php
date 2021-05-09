<?php

namespace Illuminate\Queue;

class LuaScripts
{
    /**
     * Get the Lua script for computing the size of queue.
     *
     * KEYS[1] - The name of the primary queue
     * KEYS[2] - The name of the "delayed" queue
     * KEYS[3] - The name of the "reserved" queue
     *
     * @return string
     */
    public static function size()
    {
        return <<<'LUA'
return redis.call('llen', KEYS[1]) + redis.call('zcard', KEYS[2]) + redis.call('zcard', KEYS[3])
LUA;
    }

    /**
     * Get the Lua script for pushing sportevents onto the queue.
     *
     * KEYS[1] - The queue to push the sportevent onto, for example: queues:foo
     * KEYS[2] - The notification list fot the queue we are pushing sportevents onto, for example: queues:foo:notify
     * ARGV[1] - The sportevent payload
     *
     * @return string
     */
    public static function push()
    {
        return <<<'LUA'
-- Push the sportevent onto the queue...
redis.call('rpush', KEYS[1], ARGV[1])
-- Push a notification onto the "notify" queue...
redis.call('rpush', KEYS[2], 1)
LUA;
    }

    /**
     * Get the Lua script for popping the next sportevent off of the queue.
     *
     * KEYS[1] - The queue to pop sportevents from, for example: queues:foo
     * KEYS[2] - The queue to place reserved sportevents on, for example: queues:foo:reserved
     * KEYS[3] - The notify queue
     * ARGV[1] - The time at which the reserved sportevent will expire
     *
     * @return string
     */
    public static function pop()
    {
        return <<<'LUA'
-- Pop the first sportevent off of the queue...
local sportevent = redis.call('lpop', KEYS[1])
local reserved = false

if(sportevent ~= false) then
    -- Increment the attempt count and place sportevent on the reserved queue...
    reserved = cjson.decode(sportevent)
    reserved['attempts'] = reserved['attempts'] + 1
    reserved = cjson.encode(reserved)
    redis.call('zadd', KEYS[2], ARGV[1], reserved)
    redis.call('lpop', KEYS[3])
end

return {sportevent, reserved}
LUA;
    }

    /**
     * Get the Lua script for releasing reserved sportevents.
     *
     * KEYS[1] - The "delayed" queue we release sportevents onto, for example: queues:foo:delayed
     * KEYS[2] - The queue the sportevents are currently on, for example: queues:foo:reserved
     * ARGV[1] - The raw payload of the sportevent to add to the "delayed" queue
     * ARGV[2] - The UNIX timestamp at which the sportevent should become available
     *
     * @return string
     */
    public static function release()
    {
        return <<<'LUA'
-- Remove the sportevent from the current queue...
redis.call('zrem', KEYS[2], ARGV[1])

-- Add the sportevent onto the "delayed" queue...
redis.call('zadd', KEYS[1], ARGV[2], ARGV[1])

return true
LUA;
    }

    /**
     * Get the Lua script to migrate expired sportevents back onto the queue.
     *
     * KEYS[1] - The queue we are removing sportevents from, for example: queues:foo:reserved
     * KEYS[2] - The queue we are moving sportevents to, for example: queues:foo
     * KEYS[3] - The notification list for the queue we are moving sportevents to, for example queues:foo:notify
     * ARGV[1] - The current UNIX timestamp
     *
     * @return string
     */
    public static function migrateExpiredsportevents()
    {
        return <<<'LUA'
-- Get all of the sportevents with an expired "score"...
local val = redis.call('zrangebyscore', KEYS[1], '-inf', ARGV[1])

-- If we have values in the array, we will remove them from the first queue
-- and add them onto the destination queue in chunks of 100, which moves
-- all of the appropriate sportevents onto the destination queue very safely.
if(next(val) ~= nil) then
    redis.call('zremrangebyrank', KEYS[1], 0, #val - 1)

    for i = 1, #val, 100 do
        redis.call('rpush', KEYS[2], unpack(val, i, math.min(i+99, #val)))
        -- Push a notification for every sportevent that was migrated...
        for j = i, math.min(i+99, #val) do
            redis.call('rpush', KEYS[3], 1)
        end
    end
end

return val
LUA;
    }

    /**
     * Get the Lua script for removing all sportevents from the queue.
     *
     * KEYS[1] - The name of the primary queue
     * KEYS[2] - The name of the "delayed" queue
     * KEYS[3] - The name of the "reserved" queue
     * KEYS[4] - The name of the "notify" queue
     *
     * @return string
     */
    public static function clear()
    {
        return <<<'LUA'
local size = redis.call('llen', KEYS[1]) + redis.call('zcard', KEYS[2]) + redis.call('zcard', KEYS[3])
redis.call('del', KEYS[1], KEYS[2], KEYS[3], KEYS[4])
return size
LUA;
    }
}
