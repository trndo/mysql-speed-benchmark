-- Function to generate random data
function random_string(length)
    local charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
    local result = ""
    for i = 1, length do
        local randomIndex = math.random(1, #charset)
        result = result .. string.sub(charset, randomIndex, randomIndex)
    end
    return result
end

-- Initialize the Sysbench benchmark
function event()
    local username = random_string(10)
    local email = username .. "@example.com"
    local birth_date = os.date("%Y-%m-%d", math.random(os.time() - 86400 * 365 * 30, os.time()))

    db_query(string.format(
         "INSERT INTO users (username, email, birth_date) VALUES ('%s', '%s', '%s')",
         username, email, birth_date
     )
    )
end