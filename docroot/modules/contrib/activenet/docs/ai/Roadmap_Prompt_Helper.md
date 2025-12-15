# Roadmap Prompt Helper

This document provides guidelines for a human editor reviewing the AI-generated `Roadmap.md` file for the ActiveNet Drupal module. The AI-generated roadmap is a potential starting point, often containing generic development phases. Your task is to refine and customize it to accurately reflect the specific plans, vision, and technical considerations of this ActiveNet integration module within the Open Y ecosystem.

**Goal:** Transform the generic AI roadmap into a concrete, actionable plan for the ActiveNet module's development and maintenance.

---

**Review and Customization Steps:**

1.  **Understand the Module's Current State:**
    *   Review the `README.md` and the initial description provided.
    *   Examine the existing code structure (`src/ActivenetClient.php`, `src/ActivenetClientFactory.php`, `src/Form/SettingsForm.php`, etc.) to understand its core functionality: connecting to the ActiveNet API via Guzzle, providing a client service, managing API keys and base URI via a configuration form, and exposing specific methods (`getCenters`, `getActivities`, `getActivityDetail`, etc.).
    *   Look at the `git_log_analysis_summary.md` (if generated) to understand recent development focus and history (e.g., Drupal version support, adding methods, refactoring).

2.  **Contextualize Generic Phases:**
    *   The AI might suggest phases like "Phase 1: Core Functionality", "Phase 2: Enhancements", "Phase 3: Optimization", "Phase 4: Maintenance". Replace or refine these with terms specific to the ActiveNet module's journey. For example:
        *   "Initial API Integration" (already largely complete)
        *   "Expanding API Coverage" (adding more `get` methods or handling different API types)
        *   "Integrating API Data with Drupal" (displaying ActiveNet data on the site, perhaps using Drupal entities or custom blocks/pages)
        *   "Performance & Caching Improvements"
        *   "Error Handling & Monitoring"
        *   "Advanced Features" (e.g., search, filtering, potentially write operations if supported and needed)

3.  **Incorporate Specific Module Features and Technical Details:**
    *   **Configuration:** Ensure the roadmap includes plans related to the settings form (`admin/openy/integrations/activenet/settings`). Are there plans for more settings? Validation improvements?
    *   **API Client Methods:** The existing `ActivenetClient` provides specific `get*` methods. The roadmap should detail plans for implementing *additional* required ActiveNet API endpoints if the existing ones are insufficient for planned features.
    *   **Data Usage:** How will the data fetched from ActiveNet be *used* on the Drupal site? The roadmap should reflect features that consume this data (e.g., displaying centers, listing activities, showing activity details).
    *   **Drupal Integration:** Consider how the ActiveNet data integrates with Drupal's features (Views, Blocks, custom entity types, search integration). These should be explicit roadmap items.
    *   **Dependencies:** The module uses Guzzle. Roadmap items could include keeping dependencies updated or exploring alternative HTTP clients if necessary.
    *   **Error Handling:** The module has a custom exception. Plans for more robust error handling, logging, and user feedback should be considered.

4.  **Align with Project Vision and Priorities:**
    *   Consider the module's role within the broader "Open Y" package. How does it contribute to the overall goals?
    *   Refer to the `Priorities.md` (if generated). Ensure the roadmap items are ordered or weighted according to the defined priorities. If "performance" is a high priority, optimization phases should be prominent. If "full API coverage" is a priority, adding missing methods should be planned early.

5.  **Integrate Insights from Other AI Analyses:**
    *   **Refactoring:** Review the `Refactoring_Tactics.md` and `refactor_large_files_plan.md` (if generated). Schedule planned refactoring tasks into the roadmap timeline. For instance, if the client class is identified as needing refactoring, add "Refactor ActivenetClient" as a specific step in an appropriate phase.
    *   **Code Structure:** Use insights from `code_structure_analysis.md` to identify areas that might require future work or could influence the roadmap (e.g., complex areas, potential for splitting classes).

6.  **Identify and Add Missing Features/Improvements:**
    *   Think about common requirements for API integrations that might not be present yet:
        *   Caching strategies for API responses to reduce external calls.
        *   Handling rate limits if the API has them.
        *   More granular filtering or sorting options for list endpoints based on user needs.
        *   Pagination support for endpoints returning large datasets.
        *   Potentially integrating write operations (e.g., registration) if the ActiveNet API and project scope allow (though this is a significant undertaking).
        *   Automated testing (unit tests for the client, integration tests).
        *   CI/CD integration specific to the module.
        *   Detailed documentation for developers using the client (`README.md` can be updated for this, potentially referencing the roadmap).

7.  **Include Maintenance and Operational Items:**
    *   Regular security reviews.
    *   Keeping up with Drupal core and dependency updates.
    *   Monitoring API health and error rates.
    *   Responding to user feedback or bug reports.
    *   Refining documentation.

8.  **Refine Language and Formatting:**
    *   Use clear, concise language.
    *   Structure the roadmap logically (e.g., phases, milestones, specific tasks).
    *   Use Markdown formatting for readability.

9.  **Review and Validate:**
    *   Does the roadmap align with the actual project plans and resources?
    *   Is it realistic?
    *   Is it easy to understand?
    *   Consider seeking input from other developers or stakeholders involved in the project.

By following these guidelines, you can transform a generic AI-generated roadmap into a valuable, project-specific document guiding the future development of the ActiveNet module.