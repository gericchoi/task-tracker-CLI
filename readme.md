# Task CLI 
A simple command-line task tracker application built with PHP. This project is based on the roadmap.sh(https://roadmap.sh/projects/task-tracker
) Task Tracker challenge.

## Overview

Task CLI is a command-line interface tool that helps you track and manage your tasks. You can add, update, and delete tasks, mark their progress status, and list them based on different criteria. All tasks are stored in a JSON file in the current directory.

## Features

- Add new tasks with customizable titles
- Update existing task descriptions
- Delete tasks
- Mark tasks as to-do, in-progress, or done
- List all tasks
- Filter tasks by status (to-do, in-progress, done)
- Automatic tracking of creation and update timestamps
- No external dependencies - built with native PHP functions

## Requirements

- PHP 7.0 or higher

## Installation

1. Clone this repository:

   ```
   git clone https://github.com/yourusername/task-cli.git
   ```

2. Make the script executable (Linux/Mac):

   ```
   chmod +x task-cli.php
   ```

3. Create a symbolic link to use the command globally (optional):
   ```
   sudo ln -s /path/to/task-cli.php /usr/local/bin/task-cli
   ```

## Usage

### Add a new task

```
php task-cli.php add "Complete the project"
```

The task will be created with "to-do" status by default.

### List all tasks

```
php task-cli.php list
```

### List tasks by status

```
php task-cli.php list to-do
php task-cli.php list in-progress
php task-cli.php list done
```

### Update a task

```
php task-cli.php update [task_id] "New task description"
```

### Delete a task

```
php task-cli.php delete [task_id]
```

### Change task status

```
php task-cli.php mark-to-do [task_id]
php task-cli.php mark-in-progress [task_id]
php task-cli.php mark-done [task_id]
```

## Data Storage

Task CLI stores all tasks in a `tasks.json` file in the same directory as the script. This file is automatically created if it doesn't exist. Each task contains:

- `id`: A unique identifier for the task
- `title`: A description of the task
- `status`: The status of the task (to-do, in-progress, done)
- `createdAt`: The date and time when the task was created
- `updatedAt`: The date and time when the task was last updated

## Example Workflow

```bash
# Adding a new task
php task-cli.php add "Buy groceries"
# Output: Task has been added successfully.

# Listing all tasks
php task-cli.php list
# Output: Shows all tasks with their IDs, titles, statuses, and timestamps

# Marking a task as in progress (assuming task ID is 64a7b2c3d4e5f)
php task-cli.php mark-in-progress 64a7b2c3d4e5f
# Output: Task has been updated successfully.

# Updating a task description
php task-cli.php update 64a7b2c3d4e5f "Buy groceries and prepare dinner"
# Output: Task has been updated successfully.

# Marking a task as done
php task-cli.php mark-done 64a7b2c3d4e5f
# Output: Task has been updated successfully.

# Listing only completed tasks
php task-cli.php list done
# Output: Shows only tasks with "done" status

# Deleting a task
php task-cli.php delete 64a7b2c3d4e5f
# Output: Task has been deleted successfully.
```

## Project Structure

- `task-cli.php`: The main PHP script that handles all task management operations
- `tasks.json`: JSON file that stores all tasks (automatically created when adding first task)

## Implementation Details

- No external libraries are used - only native PHP functions
- Tasks are stored in a JSON file for persistence
- Error handling is implemented to gracefully handle edge cases
- The application uses command-line arguments to determine actions and inputs

## About This Project

This project was completed as part of the roadmap.sh challenges to practice:

- Working with filesystem operations
- Handling command-line arguments and user input
- Building a practical CLI application
- Managing data persistence with JSON

## License

This project is open source and available under the MIT License.
