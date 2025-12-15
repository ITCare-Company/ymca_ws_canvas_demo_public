# ActiveNet Module Priorities

Based on the analysis of the codebase structure, dependencies, file names, comments, method signatures, and the provided git log, the following priorities are suggested for development and maintenance:

## High Priority

These items address fundamental issues affecting code correctness, maintainability, and adherence to Drupal/PHP best practices. The lack of recent commits addressing these core structural elements suggests they are current technical debt.

1.  **Implement `ActivenetClientInterface`:** The current interface (`src/ActivenetClientInterface.php`) is empty. It needs to explicitly define all public methods (`getCenters`, `getSites`, etc.) provided by the `ActivenetClient`. This is crucial for type hinting, mocking, and clearly defining the client's contract. (Reference: Code structure shows an empty interface exists but is unused effectively).
2.  **Correct `ActivenetClientFactoryInterface` Return Type:** The factory interface (`src/ActivenetClientFactoryInterface.php`) currently declares it returns `\GuzzleHttp\Client`. It should declare that it returns `\Drupal\activenet\ActivenetClientInterface` (once the interface is properly implemented) or at least `\Drupal\activenet\ActivenetClient`. (Reference: Code shows the factory returns `ActivenetClient`, not just the base `GuzzleHttp\Client`).
3.  **Refactor Magic `__call` Method:** Relying heavily on the `__call` magic method (`src/ActivenetClient.php`) hides the API surface, makes static analysis difficult, and prevents proper interface definition. Convert the `switch` cases within `__call` into explicit public methods (e.g., `public function getCenters(array $args = [])`). The `getActivityDetail` method is already explicit, indicating this is a possible pattern. (Reference: Code structure shows many methods implemented via `__call`, `getActivityDetail` is separate).
4.  **Update Documentation Example:** The README's example usage (`$client = new $ActiveNetClient();`) is incorrect for Drupal dependency injection. It should show how to get the client service, e.g., `\Drupal::service('activenet.client')`. This will improve usability and developer experience. (Reference: README shows incorrect instantiation; `activenet.services.yml` shows the client is a service).
5.  **Consolidate Client Configuration:** The current setup uses the factory to pass Guzzle configuration to the constructor *and* calls `setApi` to pass API-specific settings. This seems redundant. All necessary configuration (`base_uri`, `api_key`, headers, etc.) should ideally be passed via the client's constructor for clarity and consistency. (Reference: Code shows dual configuration path in `ActivenetClientFactory` and `ActivenetClient`).

## Medium Priority

These items address areas of potential fragility, inconsistency, or missing features that could improve the module's robustness and usability.

1.  **Improve `makeRequest` Robustness:** The `makeRequest` method specifically checks for `$object->body` in the JSON response. This is potentially fragile if the ActiveNet API changes its response structure or uses a different key. Consider making the expected data key configurable or implementing more flexible response parsing. Also, refine exception handling to be more specific than catching a generic `\Exception`. (Reference: `src/ActivenetClient.php` shows the specific `$object->body` check).
2.  **Explicit Pagination/Query Parameter Handling:** While the factory sets a `page_info` header and the `__call` method accepts `$args` for query parameters, the client doesn't offer explicit, documented methods for controlling pagination (e.g., specifying page number, limit per call dynamically). Add methods or clearer documentation/examples on how to handle pagination or filter results using method arguments. (Reference: Factory sets `page_info` header; `__call` accepts `$args` but usage for common API parameters isn't explicit).
3.  **Ensure URI Consistency:** Verify and standardize how endpoints are constructed, noting the slight difference in pattern between the `__call` methods (`endpoint`) and `getActivityDetail` (`endpoint/{id}`). (Reference: `src/ActivenetClient.php` shows different URI patterns for `__call` vs. `getActivityDetail`).
4.  **Add Missing Comments:** Add or improve code comments for methods, properties, and logical blocks (especially after refactoring `__call`) to explain their purpose, parameters, return values, and any potential side effects or exceptions. (Reference: Some parts are commented, others could use more detail).

## Lower Priority

These items represent potential future enhancements or good practices that are not critical for the module's basic functionality but would improve its quality and testability.

1.  **Add Automated Tests:** Implement unit tests for the `ActivenetClient` logic (mocking HTTP requests) and potentially kernel/functional tests for the form and service integration. (Reference: Git log doesn't indicate extensive testing specific to the client logic).
2.  **Explore Caching Mechanisms:** For performance-critical applications, consider implementing caching for API responses, especially for data that doesn't change frequently (like lists of centers, sites, activity types). (Reference: No caching mechanism is apparent in the provided code).
3.  **Expand API Coverage:** Based on ActiveNet API documentation, add client methods for any other useful endpoints not currently covered. (Reference: The list of methods in the README/code covers common listing endpoints, but the full API surface is unknown from the provided context).