# Web Summer Camp 2024 - Workshop

The workshop is divided into multiple theory and practice parts.
For every practical part is set up as a separate challenge, and there
are corresponding branches in this repository that you can easily
merge them into your current working branch.

If you get stuck, you can always check the `solution` branch.

## Challenge No. 0: Setup

**Task**: Set up the Symfony application and run it.

* Follow the setup instructions from [README.md](README.md).
* Check if the application is running by opening it in your browser.
* Run `bin/check` to see if everything is green:
  ```
            _ _      _____ _               _            _____                       _
      /\   | | |    / ____| |             | |          |  __ \                     | |
     /  \  | | |   | |    | |__   ___  ___| | _____    | |__) |_ _ ___ ___  ___  __| |
    / /\ \ | | |   | |    | '_ \ / _ \/ __| |/ / __|   |  ___/ _\ / __/ __|/ _ \/ _\ |
   / ____ \| | |   | |____| | | |  __/ (__|   <\__ \   | |  | (_| \__ \__ \  __/ (_| |
  /_/    \_\_|_|    \_____|_| |_|\___|\___|_|\_\___/   |_|   \__,_|___/___/\___|\__,_|
  ```

## Challenge No. 1: GPT

**Task**: Integrate basic GPT model with your chatbot.

* Merge branch `1-gpt` into your working branch - quality checks will now fail.
  ```shell
  git fetch origin
  git merge origin/1-gpt
  bin/check # will fail
  ```
* Implement the `App\OpenAI\GptClient` implementing the `App\OpenAI\GptClientInterface`
  * Use [OpenAI's GPT API](https://platform.openai.com/docs/api-reference/chat) and [Symfony's HttpClient](https://symfony.com/doc/current/http_client.html).
  * Example request:
    ```shell
    curl https://api.openai.com/v1/chat/completions \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $OPENAI_API_KEY" \
      -d '{
        "model": "gpt-4o",
        "temperature": 1.0,
        "messages": [
          {"role": "user", "content": "Hello!"}
        ]
      }'
    ```
  * You can find an example API response in `tests/fixtures/gpt-response.json`. 
  * Verify the implementation by running the corresponding unit test:
    ```shell
    docker compose exec app vendor/bin/phpunit tests/OpenAI/GptClientTest.php
    ```
* Use the `App\OpenAI\GptClientInterface` in the `App\Chat::submitMessage` implementation.
  * Verify the implementation by running the corresponding functional test:
    ```shell
    docker compose exec app vendor/bin/phpunit tests/Twig/ChatComponentTest.php
    ```
* Run `bin/check` to check all tests and quality tools:
  ```
            _ _      _____ _               _            _____                       _
      /\   | | |    / ____| |             | |          |  __ \                     | |
     /  \  | | |   | |    | |__   ___  ___| | _____    | |__) |_ _ ___ ___  ___  __| |
    / /\ \ | | |   | |    | '_ \ / _ \/ __| |/ / __|   |  ___/ _\ / __/ __|/ _ \/ _\ |
   / ____ \| | |   | |____| | | |  __/ (__|   <\__ \   | |  | (_| \__ \__ \  __/ (_| |
  /_/    \_\_|_|    \_____|_| |_|\___|\___|_|\_\___/   |_|   \__,_|___/___/\___|\__,_|
  ```

## Challenge No. 2: Context

**Task**: Extend the chat's context to bring in more knowledge.

* Merge branch `2-context` into your working branch - quality checks will now fail.
  ```shell
  git fetch origin
  git merge origin/2-context
  bin/check # will fail
  ```
* For extending the chat's context, implement two decorators for `App\OpenAI\GptClientInterface`:
  * `App\OpenAI\ProgramAwareClient`
  * `App\OpenAI\DateTimeAwareClient` 
* For `ProgramAwareClient` use the program of Web Summer Camp stored in `wsc-program.txt`
  * Inject the program as a **system prompt**, that tells GPT to use it for answering the user's questions.
  * Additionally think about general information that could be useful for the chatbot, e.g. location or date.
* For `DateTimeAwareClient` inject the current date & time into the context, but make sure it's always up-to-date.
  * Use Symfony's `ClockInterface` to get the current date & time.
  * Format the output so the message reads like:
    ```
    Current date is 2024-07-05 (YYYY-MM-DD) and the time is 14:00:00 (HH:MM:SS).
    ```
* Run `bin/check` to check all tests and quality tools:
  ```
            _ _      _____ _               _            _____                       _
      /\   | | |    / ____| |             | |          |  __ \                     | |
     /  \  | | |   | |    | |__   ___  ___| | _____    | |__) |_ _ ___ ___  ___  __| |
    / /\ \ | | |   | |    | '_ \ / _ \/ __| |/ / __|   |  ___/ _\ / __/ __|/ _ \/ _\ |
   / ____ \| | |   | |____| | | |  __/ (__|   <\__ \   | |  | (_| \__ \__ \  __/ (_| |
  /_/    \_\_|_|    \_____|_| |_|\___|\___|_|\_\___/   |_|   \__,_|___/___/\___|\__,_|
  ```

## Challenge No. 3: Vectors

**Task**: Convert WSC program into embedding vectors in ChromaDB.

* Merge branch `3-vectors` into your working branch.
  ```shell
  git fetch origin
  git merge origin/3-vectors
  bin/check # will fail
  ```
* Start the ChromaDB container and run the test command.
  ```shell
  docker compose exec app bin/console app:test:chroma
  ```
  Should output ChromaDB version, collection details and end with an error.
* Implement `App\OpenAI\EmbeddingClient::create` method
  * Example request:
    ```shell
    curl https://api.openai.com/v1/embeddings \
      -H "Authorization: Bearer $OPENAI_API_KEY" \
      -H "Content-Type: application/json" \
      -d '{
        "input": "The food was delicious and the waiter...",
        "model": "text-embedding-ada-002",
        "encoding_format": "float"
      }'
    ``` 
  * See [OpenAI's Embeddings API](https://platform.openai.com/docs/api-reference/embeddings) for more information.
  * Afterward, run the test command again.
* Implement `App\WscProgram\Embedder` to complete `app:program:embed` command.
  * The `App\WscProgram\Loader` can take care of loading the program from the website for you.
  * You will receive a `Program` with a list of `Session` objects.
  * The `Session` object has a `toString()` and a `toArray()` that are helpful for creating the vector and metadata.
  * For interaction with ChromaDB, use the `Codewithkyrian\ChromaDB\Client` class, which is already prepared to be injected.
  * See [documentation](https://github.com/CodeWithKyrian/chromadb-php) for information on how to use the ChromaDB client.
* After successfully running the command `app:program:embed`, run the test command again.
  ```shell
  docker compose exec app bin/console app:program:embed -vv
  docker compose exec app bin/console app:test:chroma
  ```
  After ChromaDB details, you should see a list of similar content:
  ```shell
  // Searching for Symfony content ...
  
  -------------------------------------- ------------------------------------------------------------------ 
   ID                                     Title
  -------------------------------------- ------------------------------------------------------------------ 
   229ec03a-8b73-4109-94e8-107163aca923   Demystify Symfony - Understanding the functions of the framework  
   751656e0-108b-470f-ba6d-fb4f7adf71c9   Enjoying frontend with Symfony UX                                 
   0b64cc5e-6431-4566-bb9f-30bf4727e624   Testing Symfony applications with Codeception                     
   ff76a852-606e-411d-9b43-1b0d0e54c354   Custom Chatbots with GPT and Symfony PHP framework
  -------------------------------------- ------------------------------------------------------------------ 
  ```
* And of course, also make sure `bin/check` passes all tests and tools:
  ```
            _ _      _____ _               _            _____                       _
      /\   | | |    / ____| |             | |          |  __ \                     | |
     /  \  | | |   | |    | |__   ___  ___| | _____    | |__) |_ _ ___ ___  ___  __| |
    / /\ \ | | |   | |    | '_ \ / _ \/ __| |/ / __|   |  ___/ _\ / __/ __|/ _ \/ _\ |
   / ____ \| | |   | |____| | | |  __/ (__|   <\__ \   | |  | (_| \__ \__ \  __/ (_| |
  /_/    \_\_|_|    \_____|_| |_|\___|\___|_|\_\___/   |_|   \__,_|___/___/\___|\__,_|
  ```

## Challenge No. 4: Retrieval

**Task**: Switch to retrieval augmented generation (RAG) instead of static context.

* Merge branch `4-retrieval` into your working branch.
  ```shell
  git fetch origin
  git merge origin/4-retrieval
  bin/check # will fail
  ```
* Implement `App\OpenAI\RetrievalClient` decorator to replace `ProgramAwareClient`
  * Define system prompt with instructions and general information.
  * Use the `App\OpenAI\EmbeddingClient::create` to get the embedding vectors for the user's message.
  * Use the `Codewithkyrian\ChromaDB\Client::query` to search for similar content in ChromaDB.
    * See `App\Command\ChromaTestCommand` for an example query.
  * Convert the search results into an assistant message
    * See `App\WscProgram\Data\Session::fromArray($metadata)->toString()` for easy conversion.
* Run `bin/check` to check all tests and quality tools:
  ```
            _ _      _____ _               _            _____                       _
      /\   | | |    / ____| |             | |          |  __ \                     | |
     /  \  | | |   | |    | |__   ___  ___| | _____    | |__) |_ _ ___ ___  ___  __| |
    / /\ \ | | |   | |    | '_ \ / _ \/ __| |/ / __|   |  ___/ _\ / __/ __|/ _ \/ _\ |
   / ____ \| | |   | |____| | | |  __/ (__|   <\__ \   | |  | (_| \__ \__ \  __/ (_| |
  /_/    \_\_|_|    \_____|_| |_|\___|\___|_|\_\___/   |_|   \__,_|___/___/\___|\__,_|
  ```

## Challenge No. 5: Tools

**Task**: Shift time and program search to tools.

* Merge branch `5-tools` into your working branch.
  ```shell
  git fetch origin
  git merge origin/5-tools
  bin/check # will fail
  ```
* Use `PhpLlm\LlmChain\ToolChain` instead of `GptClientInterface` in `App\Chat`
  * Adopt `PhpLlm\Message\Message` and `PhpLlm\Message\MessageBag` instead of `array` for `$messages`
  ```php
  $messages = new MessageBag();
  $messages[] = Message::ofUser('What time is it?');
  $messages[] = Message::ofAssistant($response);
  ```
* Implement `App\Tool\Clock` to provide current date & time
  * Use Symfony's `ClockInterface` to get the current date & time.
  * Use `PhpLlm\LlmChain\ToolBox\AsTool` to expose service as tool.
  ```php
  #[AsTool('clock', 'Provides the current date and time')]
  ```
  * Check the registration via `debug:container` command.
  ```shell
  docker compose exec app bin/console debug:container --tag llm_chain.tool
  ```
* Implement `App\Tool\Retriever` to provide a search for program sessions.
  * You can basically reuse a lot of the code of the `App\OpenAI\RetrievalClient` here.
* Run `bin/check` to check all tests and quality tools:
  ```
            _ _      _____ _               _            _____                       _
      /\   | | |    / ____| |             | |          |  __ \                     | |
     /  \  | | |   | |    | |__   ___  ___| | _____    | |__) |_ _ ___ ___  ___  __| |
    / /\ \ | | |   | |    | '_ \ / _ \/ __| |/ / __|   |  ___/ _\ / __/ __|/ _ \/ _\ |
   / ____ \| | |   | |____| | | |  __/ (__|   <\__ \   | |  | (_| \__ \__ \  __/ (_| |
  /_/    \_\_|_|    \_____|_| |_|\___|\___|_|\_\___/   |_|   \__,_|___/___/\___|\__,_|
  ```

## Challenge No. 6: YouTube

**Task**: Implement a chat on top of a YouTube video transcript.

* Merge branch `6-youtube` into your working branch.
  ```shell
  git fetch origin
  git merge origin/6-youtube
  ```
* You now have a new route, that renders a new chat interface at `/youtube`.
* A Twig component `App\Twig\YouTubeComponent` and `App\YouTube\TranscriptFetcher` are already implemented.
* Finish the implementation by creating `App\YouTube`
  * Implement a `start` method to initialize the chat with a system prompt.
  * Embed the transcript of the YouTube video into the context.
  * Use `App\Chat` implementation as inspiration for session handling.
  * But use `PhpLlm\LlmChain\ChatChain`.
* In the end `bin/check` should run successful:
  ```
            _ _      _____ _               _            _____                       _
      /\   | | |    / ____| |             | |          |  __ \                     | |
     /  \  | | |   | |    | |__   ___  ___| | _____    | |__) |_ _ ___ ___  ___  __| |
    / /\ \ | | |   | |    | '_ \ / _ \/ __| |/ / __|   |  ___/ _\ / __/ __|/ _ \/ _\ |
   / ____ \| | |   | |____| | | |  __/ (__|   <\__ \   | |  | (_| \__ \__ \  __/ (_| |
  /_/    \_\_|_|    \_____|_| |_|\___|\___|_|\_\___/   |_|   \__,_|___/___/\___|\__,_|
  ```

## Challenge No. 7: Wikipedia

**Task**: Implement a tool chain on top of Wikipedia.

* Merge branch `7-wikipedia` into your working branch.
  ```shell
  git fetch origin
  git merge origin/7-wikipedia
  ```
* You now have a new route, that renders a new chat interface at `/wikipedia`.
* Also `App\Twig\WikipediaComponent` and `App\Wikipedia\Client` are already implemented.
* Implement two tools to equip your bot with the capability to search and read Wikipedia articles.
  * `App\Tool\Wikipedia::search` to search for Wikipedia articles.
  * `App\Tool\Wikipedia::read` to read the content of a Wikipedia article.
* Implement `App\Wikipedia` to bring the bot to life.
  * Integrate it with `App\Twig\WikipediaComponent`, `App\Wikipedia\Client` and `PhpLlm\LlmChain\ToolChain`. 
* In the end `bin/check` should run successful:
  ```
            _ _      _____ _               _            _____                       _
      /\   | | |    / ____| |             | |          |  __ \                     | |
     /  \  | | |   | |    | |__   ___  ___| | _____    | |__) |_ _ ___ ___  ___  __| |
    / /\ \ | | |   | |    | '_ \ / _ \/ __| |/ / __|   |  ___/ _\ / __/ __|/ _ \/ _\ |
   / ____ \| | |   | |____| | | |  __/ (__|   <\__ \   | |  | (_| \__ \__ \  __/ (_| |
  /_/    \_\_|_|    \_____|_| |_|\___|\___|_|\_\___/   |_|   \__,_|___/___/\___|\__,_|
  ```
