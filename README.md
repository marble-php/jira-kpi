## Marble KPI calculator for Jira

This is a command-line tool for calculating various types of productivity KPIs 
for software development teams using [Jira](https://www.atlassian.com/software/jira). 

It uses our [entity-manager library](https://github.com/marble-php/entity-manager)
in combination with [Doctrine DBAL](https://github.com/doctrine/dbal).

The following metrics are calculated:

- velocity
- cycle times
- development iterations (first time right)
- bug reports
- bug lead times
- bug work ratio

... and more. This library is entirely based on the Jira workflow currently in use
by our software development team at [Autarco](https://github.com/autarco).
