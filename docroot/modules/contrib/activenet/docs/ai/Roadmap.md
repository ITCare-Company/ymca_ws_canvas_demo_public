# ActiveNet Module Roadmap

This document outlines the current state and potential future development directions for the ActiveNet module, based on the provided code structure, comments, and commit history.

## Current Features & Accomplishments

Based on the module's current code and git history, the following features have been implemented:

*   **Basic API Integration:** Provides a client (`ActivenetClient`) for interacting with the ActiveNet API via HTTP GET requests.
*   **Configuration:** Allows users to configure the ActiveNet API Base URI and API Key via an administration form (`SettingsForm`).
*   **Service Registration:** The `ActivenetClient` is registered as a service (`activenet.client`) and created using a factory (`ActivenetClientFactory`), enabling dependency injection.
*   **Data Retrieval Methods:** Supports fetching the following data types from the API:
    *   Centers (`getCenters`)
    *   Sites (`getSites`)
    *   Activities (`getActivities`)
    *   Activity Types (`getActivityTypes`)
    *   Activity Categories (`getActivityCategories`)
    *   Activity Other Categories (`getActivityOtherCategories`)
    *   Flex Reg Programs (`getFlexRegPrograms`)
    *   Flex Reg Program Types (`getFlexRegProgramTypes`)
    *   Membership Packages (`getMembershipPackages`)
    *   Membership Categories (`getMembershipCategories`)
    *   Specific Activity Details by ID (`getActivityDetail`)
*   **Basic Error Handling:** Includes a custom exception class (`ActivenetClientException`) and basic error checking for HTTP status codes and empty responses within the `makeRequest` wrapper.
*   **Drupal Compatibility:** Supports Drupal 8, 9, and 10 (`core_version_requirement: ^8 || ^9 || ^10`). Updates for Drupal 10 support were explicitly added (Commit `faeb4f9`).
*   **Code Style Compliance:** Recent commits indicate efforts to resolve PHPCS issues (Commit `435bf1d`).
*   **Module Structure:** Follows standard Drupal module structure, including `info.yml`, `links.menu.yml`, `module`, `permissions.yml`, `routing.yml`, `services.yml`, `config/install`, and `src` directories.

## Potential Future Enhancements

Based on the current implementation and common patterns for API integrations, the following areas could be considered for future development:

1.  **Enhanced API Endpoint Support:**
    *   Investigate and implement support for additional ActiveNet API endpoints beyond those currently available via the `__call` method and `getActivityDetail`.
2.  **Support for Write Operations:**
    *   If the ActiveNet API supports POST, PUT, DELETE, or other HTTP methods for creating, updating, or deleting data, implement corresponding methods in the `ActivenetClient`.
3.  **Improved Error Handling:**
    *   Refine error handling within `makeRequest` to potentially parse API-specific error messages returned in the response body for more informative exceptions.
    *   Implement more granular exception types if different API error scenarios require distinct handling.
4.  **Caching Mechanism:**
    *   Implement caching for API responses (e.g., using Drupal's cache API) to reduce redundant requests to the ActiveNet API and improve performance, especially for frequently accessed endpoints like `getCenters` or `getActivities`.
5.  **Pagination and Filtering Abstraction:**
    *   Provide more explicit support and documentation for pagination and filtering parameters the ActiveNet API might support. While `__call` allows passing arguments, dedicated helper methods or standardized argument structures could simplify usage for common filtering needs.
6.  **Structured Data Output:**
    *   Consider mapping raw JSON responses into more structured PHP objects or data transfer objects (DTOs) to provide a more strongly typed and easier-to-consume data interface for developers using the client.
7.  **API Rate Limiting:**
    *   If the ActiveNet API enforces rate limits, investigate and implement strategies (e.g., request queuing, backoff algorithms) to handle rate limiting gracefully within the client.

## Code Quality and Maintenance

Areas for potential improvement in code structure and maintenance:

1.  **Replace Magic `__call` Method:**
    *   Replace the magic `__call` method with explicit public methods for each supported API endpoint (`getCenters`, `getSites`, etc.). This improves code discoverability, IDE autocompletion, static analysis, and overall maintainability.
2.  **Refine Guzzle Client Usage:**
    *   Review the pattern of extending `GuzzleHttp\Client`. While functional via the factory, consider whether injecting a `GuzzleHttp\Client` instance into the `ActivenetClient` (composition over inheritance) would be a more standard Drupal/Symfony service pattern, allowing greater flexibility (e.g., using a mock client for testing).
3.  **Add Automated Tests:**
    *   Implement unit tests for the `ActivenetClient` (potentially using Guzzle mocks) to ensure API interactions, request building, and response handling are working correctly.
    *   Consider integration tests if feasible without making live API calls.
4.  **Dependency Updates:**
    *   Regularly review and update dependencies (including Guzzle) to ensure security and compatibility.

This roadmap provides a direction for evolving the ActiveNet module, focusing on both expanding functionality and improving the underlying codebase for better maintainability and performance.