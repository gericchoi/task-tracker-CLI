<?php

// ─────────────────────────────────────────────────────────────
// Constants
// ─────────────────────────────────────────────────────────────

define('TASK_ACTIONS', [
    'added' => 'added',
    'deleted' => 'deleted',
    'updated' => 'updated',
    'listed' => 'listed'
]);

$filePath = 'tasks.json';
$argv = $_SERVER['argv'];
$command = $argv[1] ?? null;
$dateTime = date('Y-m-d H:i:s');

$validStatuses = [
    'mark-to-do' => 'to-do',
    'mark-in-progress' => 'in-progress',
    'mark-done' => 'done',
    'to-do' => 'to-do',
    'in-progress' => 'in-progress',
    'done' => 'done',
    'list-all' => 'list-all'
];

// ─────────────────────────────────────────────────────────────
// Helper Functions
// ─────────────────────────────────────────────────────────────

function readTask(string $filePath) {
    if (file_exists($filePath)) {
        return json_decode(file_get_contents($filePath), true);
    }
    return [];
}

function writeTask(string $filePath, $tasks) {
    file_put_contents($filePath, json_encode($tasks, JSON_PRETTY_PRINT));
}

function generateTaskId() {
    return uniqid();
}

function setTask(array &$tasks, int $taskNumber, array $updates): void {
    foreach ($updates as $key => $value) {
        $tasks[$taskNumber][$key] = $value;
    }
}

function successMessage($action) {
    $message = "Task has been " . TASK_ACTIONS[$action] . " successfully.\n";
    echo $message;
}

// ─────────────────────────────────────────────────────────────
// Validation Functions
// ─────────────────────────────────────────────────────────────

function checkIfTasksExist($tasks) {
    if (empty($tasks)) {
        echo 'No tasks in the list.';
        exit(1);
    }
}

function validateTaskId($inputId) {
    if (empty($inputId)) {
        echo "Invalid task ID can't be empty.";
        exit(1);
    }
}

function validateStatusInput($statusInput, $validStatuses) {
    $normalized = strtolower(trim($statusInput));

    if (!isset($validStatuses[$normalized])) {
        echo "Invalid status input: $statusInput\n";
        exit(1);
    }
}

// ─────────────────────────────────────────────────────────────
// Search Function
// ─────────────────────────────────────────────────────────────

function findTask(string $filePath, $inputId) {
    $tasks = readTask($filePath);

    if (!is_array($tasks)) {
        $tasks = [];
    }

    foreach ($tasks as $index => $task) {
        if ($task["id"] == $inputId) {
            return $index;
        }
    }

    echo "Error: Task with ID '{$inputId}' not found.\n";
    exit(1);
}

// ─────────────────────────────────────────────────────────────
// Display Functions
// ─────────────────────────────────────────────────────────────

function displayAllTask(array &$tasks): void {
    foreach ($tasks as $key => $task) {
        echo '#: ' . $key . "\n";
        echo 'ID: ' . $task['id'] . "\n";
        echo 'Title: ' . $task['title'] . "\n";
        echo 'Status: ' . $task['status'] . "\n";
        echo 'Created At: ' . $task['createdAt'] . "\n";
        echo 'Updated At: ' . $task['updatedAt'] . "\n\n";
    }

    if (empty($tasks)) {
        echo "No task in the list.";
        exit(1);
    }
}

function displayTaskByStatus(array &$tasks, $statusInput): void {
    $found = false;

    foreach ($tasks as $key => $task) {
        if ($task['status'] === $statusInput) {
            echo '#: ' . $key . "\n";
            echo 'ID: ' . $task['id'] . "\n";
            echo 'Title: ' . $task['title'] . "\n";
            echo 'Status: ' . $task['status'] . "\n";
            echo 'Created At: ' . $task['createdAt'] . "\n";
            echo 'Updated At: ' . $task['updatedAt'] . "\n\n";
            $found = true;
        }
    }

    if (!$found) {
        echo "There is no current task with status - $statusInput\n";
        exit(1);
    }
}

// ─────────────────────────────────────────────────────────────
// Task Actions
// ─────────────────────────────────────────────────────────────

function addTask($filePath, $dateTime, $argv = []) {
    $action = 'added';

    if (!file_exists($filePath)) {
        file_put_contents($filePath, json_encode([], JSON_PRETTY_PRINT));
    }

    $tasks = readTask($filePath);
    $task = $argv[2] ?? null;
    $statusInput = $argv[3] ?? 'to-do';

    if (!is_array($tasks)) {
        $tasks = [];
    }

    $id = generateTaskId();

    $tasks[] = [
        'id' => $id,
        'title' => $task,
        'status' => $statusInput,
        'createdAt' => $dateTime,
        'updatedAt' => $dateTime
    ];

    writeTask($filePath, $tasks);
    successMessage($action);
}

function updateTask($filePath, $dateTime, $argv = []) {
    $action = 'updated';
    $tasks = readTask($filePath);
    checkIfTasksExist($tasks);

    $inputId = $argv[2] ?? null;
    validateTaskId($inputId);

    $taskNumber = findTask($filePath, $inputId);
    $newTask = $argv[3] ?? null;

    setTask($tasks, $taskNumber, [
        'title' => $newTask,
        'updatedAt' => $dateTime,
    ]);

    writeTask($filePath, $tasks);
    successMessage($action);
}

function deleteTask($filePath, $argv = []) {
    $action = 'deleted';
    $tasks = readTask($filePath);
    checkIfTasksExist($tasks);

    $inputId = $argv[2] ?? null;
    validateTaskId($inputId);

    $taskNumber = findTask($filePath, $inputId);
    unset($tasks[$taskNumber]);
    $tasks = array_values($tasks);

    writeTask($filePath, $tasks);
    successMessage($action);
}

function showTask($filePath, $argv = [], $validStatuses) {
    $action = 'listed';
    $tasks = readTask($filePath);
    checkIfTasksExist($tasks);

    $statusInput = $argv[2] ?? 'list-all';
    validateStatusInput($statusInput, $validStatuses);

    if ($statusInput === 'list-all') {
        displayAllTask($tasks);
    } else {
        $actualStatus = $validStatuses[$statusInput];
        displayTaskByStatus($tasks, $actualStatus);
    }

    successMessage($action);
}

function markStatus($filePath, $dateTime, $argv = [], $validStatuses) {
    $action = "updated";
    $tasks = readTask($filePath);
    checkIfTasksExist($tasks);

    $statusInput = $argv[1] ?? null;
    $inputId = $argv[2] ?? null;

    validateTaskId($inputId);
    $taskNumber = findTask($filePath, $inputId);
    validateStatusInput($statusInput, $validStatuses);

    $newStatus = $validStatuses[$statusInput];

    setTask($tasks, $taskNumber, [
        'status' => $newStatus,
        'updatedAt' => $dateTime,
    ]);

    writeTask($filePath, $tasks);
    successMessage($action);
}

// ─────────────────────────────────────────────────────────────
// Command Routing
// ─────────────────────────────────────────────────────────────

switch ($command) {
    case 'add':
        addTask($filePath, $dateTime, $argv);
        break;
    case 'list':
        showTask($filePath, $argv, $validStatuses);
        break;
    case 'update':
        updateTask($filePath, $dateTime, $argv);
        break;
    case 'delete':
        deleteTask($filePath, $argv);
        break;
    case 'mark-to-do':
    case 'mark-in-progress':
    case 'mark-done':
        markStatus($filePath, $dateTime, $argv, $validStatuses);
        break;
    default:
        echo "Usage:\n";
        echo "  task-cli add [task_title] [status]       - Add a task with optional status (default: to-do)\n";
        echo "  task-cli list [status]                   - List tasks by status (to-do | in-progress | done)\n";
        echo "  task-cli list                            - List all tasks\n";
        echo "  task-cli update [task_id] [new_title]    - Update a task's title\n";
        echo "  task-cli delete [task_id]                - Delete a task by ID\n";
        echo "  task-cli mark-to-do [task_id]            - Mark a task as to-do\n";
        echo "  task-cli mark-in-progress [task_id]      - Mark a task as in-progress\n";
        echo "  task-cli mark-done [task_id]             - Mark a task as done\n";
        echo "\nStatus options: to-do | in-progress | done\n";
        break;
}
