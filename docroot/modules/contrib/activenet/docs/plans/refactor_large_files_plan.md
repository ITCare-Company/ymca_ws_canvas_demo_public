# Refactoring Plan for Activenet Module

## Introduction

This document outlines a refactoring plan based on an analysis of the provided codebase for the `activenet` module. The primary goal is to improve the readability, maintainability, and clarity of the core API client class.

## Identified Files for Refactoring

Based on the provided code structure and contents, the following files have been identified as candidates for refactoring or improvement:

1.  **`src/ActivenetClient.php`**: This class extends `GuzzleHttp\Client` and acts as the primary interface for interacting with the ActiveNet API. Its complexity stems from:
    *   Utilizing the `__call` magic method with a large `switch` statement to handle multiple API endpoints (`getCenters`, `getSites`, etc.). This makes the class's public API less explicit and harder to discover without reading the code or docblocks.
    *   A dedicated public method `getActivityDetail` exists outside the `__call` logic, leading to inconsistency in how different endpoints are accessed.
    *   The class directly contains both the HTTP client functionality (via inheritance) and the ActiveNet specific endpoint logic.

2.  **`src/ActivenetClientInterface.php`**: This interface currently exists but is empty. It needs to be populated to define the public contract of the `ActivenetClient` class, which is crucial for testability and code clarity.

## Refactoring Goal(s)

*   Make the `ActivenetClient` class's public API explicit and easily discoverable.
*   Improve the maintainability of the client by removing the reliance on the magic `__call` method.
*   Clearly define the client's contract using the `ActivenetClientInterface`.
*   Enhance code clarity and potentially prepare the class for easier testing or future enhancements.

## Refactoring Plan Steps

1.  **Populate `ActivenetClientInterface.php`**:
    *   Add method signatures for all the supported API endpoints currently handled by the `@method` docblocks and the `switch` statement in `ActivenetClient::__call`. This includes:
        *   `getSites(array $args = [])`
        *   `getCenters(array $args = [])`
        *   `getActivities(array $args = [])`
        *   `getActivityTypes(array $args = [])`
        *   `getActivityCategories(array $args = [])`
        *   `getActivityOtherCategories(array $args = [])`
        *   `getFlexRegPrograms(array $args = [])`
        *   `getFlexRegProgramTypes(array $args = [])`
        *   `getMembershipPackages(array $args = [])`
        *   `getMembershipCategories(array $args = [])`
        *   `getActivityDetail(int $id)`
    *   Ensure return types and parameter types are correctly defined based on the current implementation (e.g., `mixed` for generic responses, `int` for `$id`).

2.  **Implement Explicit Methods in `ActivenetClient.php`**:
    *   For each method signature added to the interface (Step 1), create a corresponding public method in the `ActivenetClient` class.
    *   Move the logic from the `switch` cases within the `__call` method into these new explicit methods. Each new method will call the internal `makeRequest` method with the correct endpoint URI segment and any necessary parameters (including the API key).
    *   For example, the `getCenters()` method would be implemented to call `$this->makeRequest('get', $base_uri . 'centers' . $suffix, $args[0] ?? []);`. The logic for adding the API key and building the suffix from arguments should be handled consistently, perhaps within the explicit methods or refined within `makeRequest`.

3.  **Refine Parameter Handling and API Key Injection**:
    *   Review how parameters (`$args`) are handled and how the `api_key` is added. Ensure it's consistently applied across all explicit methods. It might be cleaner to add the API key to the parameters array *before* passing it to `makeRequest`.
    *   Ensure the `apisettings` are accessible to all new methods (they are currently stored as a protected property).

4.  **Remove `__call` Magic Method**:
    *   Once all supported endpoints are handled by explicit public methods, remove the `__call` magic method and the associated `switch` statement from the `ActivenetClient` class.
    *   Remove the `@method` docblocks that were used to document the `__call` capabilities.

5.  **Update Existing Method (`getActivityDetail`)**:
    *   Ensure the existing `getActivityDetail` method is consistent with the new explicit methods in terms of parameter handling and calling `makeRequest`. Since the `__call` is removed, it should remain a dedicated public method, now included in the interface.

6.  **Update Docblocks**:
    *   Add clear docblocks to each new explicit public method, explaining its purpose, parameters, and return value.

7.  **Review `makeRequest` Method**:
    *   While not the primary target, review the `makeRequest` method. It currently handles basic HTTP interaction, status check, and JSON decoding. Its current implementation seems reasonably encapsulated, but its error handling could potentially be expanded or made more specific in the future if needed (e.g., handling different HTTP error codes from the ActiveNet API).

## Potential Benefits

*   **Improved Readability:** The public API of the client will be immediately clear from the class definition and the interface.
*   **Increased Maintainability:** Changes to specific API calls will be isolated within their dedicated methods, rather than modifying a large `switch` statement.
*   **Enhanced Testability:** Mocking or testing the client becomes easier when its methods are explicitly defined in an interface.
*   **Better Discoverability:** Developers using the class can easily see available methods via IDE autocompletion or documentation generated from the interface/docblocks.

This plan focuses on restructuring the core client logic to be more standard, explicit, and maintainable, directly addressing the complexity introduced by the `__call` magic method and the inconsistency with the separate `getActivityDetail` method.