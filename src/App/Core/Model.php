<?php

declare(strict_types=1);

namespace App\App\Core;

use App\App\Database\Database;
use PDO;
use PDOStatement;

/**
 * Base Model Class
 * 
 * Provides a clean, efficient query builder and database interaction layer.
 * Inspired by Laravel's Eloquent but streamlined for simplicity.
 */
abstract class Model
{
    /**
     * Database connection instance
     */
    private static ?PDO $db = null;

    /**
     * Table name (auto-detected from class name if not set)
     */
    protected static string $table = '';

    /**
     * Primary key column name
     */
    protected static string $primaryKey = 'id';

    /**
     * Enable automatic timestamps (created_at, updated_at)
     */
    protected static bool $timestamps = true;

    /**
     * Enable soft deletes (deleted_at column)
     */
    protected static bool $softDeletes = false;

    /**
     * Fillable columns for mass assignment
     */
    protected static array $fillable = [];

    /**
     * Guarded columns (cannot be mass assigned)
     */
    protected static array $guarded = ['id'];

    /**
     * Initialize database connection
     */
    private static function init(): PDO
    {
        if (self::$db === null) {
            self::$db = Database::initialize();
        }
        return self::$db;
    }

    /**
     * Get database connection
     */
    public static function conn(): PDO
    {
        return self::init();
    }
    protected static function getTable(): string
    {
        if (empty(static::$table)) {
            $className = get_called_class();
            $className = str_replace('App\\App\\Models\\', '', $className);
            static::$table = snakeCase($className);
        }
        return static::$table;
    }
    
    // ==================== QUERY BUILDER ====================

    /**
     * Start a SELECT query
     */
    public static function select(array|string $columns = ['*']): QueryBuilder
    {
        $columns = is_array($columns) ? $columns : [$columns];
        return new QueryBuilder(static::getTable(), $columns);
    }

    /**
     * Find a record by primary key
     */
    public static function find(int|string $id): ?array
    {
        $sql = "SELECT * FROM " . static::getTable() . " WHERE " . static::$primaryKey . " = :id LIMIT 1";
        $stmt = self::conn()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Find a record by column value
     */
    public static function findBy(string $column, mixed $value): ?array
    {
        $sql = "SELECT * FROM " . static::getTable() . " WHERE {$column} = :value LIMIT 1";
        $stmt = self::conn()->prepare($sql);
        $stmt->execute(['value' => $value]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all records
     */
    public static function all(array $columns = ['*']): array
    {
        $cols = implode(', ', $columns);
        $sql = "SELECT {$cols} FROM " . static::getTable();
        $stmt = self::conn()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get records with WHERE condition
     */
    public static function where(string $column, mixed $value, string $operator = '='): QueryBuilder
    {
        $builder = new QueryBuilder(static::getTable());
        return $builder->where($column, $value, $operator);
    }

    /**
     * Count all records
     */
    public static function count(string $column = '*'): int
    {
        $sql = "SELECT COUNT({$column}) as total FROM " . static::getTable();
        $stmt = self::conn()->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Check if a record exists
     */
    public static function exists(string $column, mixed $value): bool
    {
        $sql = "SELECT 1 FROM " . static::getTable() . " WHERE {$column} = :value LIMIT 1";
        $stmt = self::conn()->prepare($sql);
        $stmt->execute(['value' => $value]);
        return (bool)$stmt->fetch();
    }
    
    // ==================== CRUD OPERATIONS ====================

    /**
     * Create a new record
     */
    public static function create(array $data): array
    {
        // Filter fillable/guarded columns
        $data = static::filterFillable($data);

        // Add timestamps
        if (static::$timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');

            // Add created_by if user is logged in
            if (function_exists('__getUser')) {
                $userId = __getUser('uid');
                if ($userId) {
                    $data['created_by'] = $userId;
                }
            }
        }

        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":{$col}", $columns);

        $sql = "INSERT INTO " . static::getTable() . " (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = self::conn()->prepare($sql);
        $stmt->execute($data);

        // Return the created record
        return static::find(self::conn()->lastInsertId());
    }

    /**
     * Update a record by primary key
     */
    public static function update(int|string $id, array $data): ?array
    {
        // Filter fillable/guarded columns
        $data = static::filterFillable($data);

        // Add updated timestamp
        if (static::$timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');

            // Add updated_by if user is logged in
            if (function_exists('__getUser')) {
                $userId = __getUser('uid');
                if ($userId) {
                    $data['updated_by'] = $userId;
                }
            }
        }

        $sets = array_map(fn($col) => "{$col} = :{$col}", array_keys($data));

        $sql = "UPDATE " . static::getTable() . " 
                SET " . implode(', ', $sets) . " 
                WHERE " . static::$primaryKey . " = :id";

        $data['id'] = $id;
        $stmt = self::conn()->prepare($sql);
        $stmt->execute($data);

        // Return the updated record
        return static::find($id);
    }

    /**
     * Delete a record by primary key
     */
    public static function delete(int|string $id): bool
    {
        if (static::$softDeletes) {
            // Soft delete
            $sql = "UPDATE " . static::getTable() . " 
                    SET deleted_at = :deleted_at 
                    WHERE " . static::$primaryKey . " = :id";
            $stmt = self::conn()->prepare($sql);
            return $stmt->execute([
                'deleted_at' => date('Y-m-d H:i:s'),
                'id' => $id
            ]);
        } else {
            // Hard delete
            $sql = "DELETE FROM " . static::getTable() . " WHERE " . static::$primaryKey . " = :id";
            $stmt = self::conn()->prepare($sql);
            return $stmt->execute(['id' => $id]);
        }
    }

    /**
     * Update specific columns by condition
     */
    public static function updateWhere(string $column, mixed $value, array $data): int
    {
        // Filter fillable/guarded columns
        $data = static::filterFillable($data);

        // Add updated timestamp
        if (static::$timestamps && !isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $sets = array_map(fn($col) => "{$col} = :{$col}", array_keys($data));

        $sql = "UPDATE " . static::getTable() . " 
                SET " . implode(', ', $sets) . " 
                WHERE {$column} = :where_value";

        $data['where_value'] = $value;
        $stmt = self::conn()->prepare($sql);
        $stmt->execute($data);

        return $stmt->rowCount();
    }
    
    // ==================== TRANSACTIONS ====================

    /**
     * Begin a database transaction
     */
    public static function beginTransaction(): bool
    {
        return self::conn()->beginTransaction();
    }

    /**
     * Commit a database transaction
     */
    public static function commit(): bool
    {
        return self::conn()->commit();
    }

    /**
     * Rollback a database transaction
     */
    public static function rollback(): bool
    {
        return self::conn()->rollBack();
    }
    
    // ==================== HELPER METHODS ====================

    /**
     * Filter data based on fillable/guarded columns
     */
    protected static function filterFillable(array $data): array
    {
        // If fillable is set, only allow those columns
        if (!empty(static::$fillable)) {
            return array_intersect_key($data, array_flip(static::$fillable));
        }

        // If guarded is set, remove those columns
        if (!empty(static::$guarded)) {
            return array_diff_key($data, array_flip(static::$guarded));
        }

        return $data;
    }

    /**
     * Execute raw SQL query
     */
    public static function raw(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::conn()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Get last inserted ID
     */
    public static function lastInsertId(): int
    {
        return (int)self::conn()->lastInsertId();
    }

    /**
     * Paginate results
     */
    public static function paginate(int $perPage = 15, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;
        $total = static::count();

        $sql = "SELECT * FROM " . static::getTable() . " LIMIT :limit OFFSET :offset";
        $stmt = self::conn()->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int)ceil($total / $perPage),
        ];
    }
}

/**
 * Query Builder Class
 * 
 * Provides fluent interface for building SQL queries
 */
class QueryBuilder
{
    private string $table;
    private array $columns = ['*'];
    private array $wheres = [];
    private array $bindings = [];
    private ?string $orderBy = null;
    private ?string $groupBy = null;
    private ?int $limit = null;
    private ?int $offset = null;
    private ?int $cacheTtl = null;
    private bool $cacheEnabled = false;

    public function __construct(string $table, array $columns = ['*'])
    {
        $this->table = $table;
        $this->columns = $columns;
    }

    /**
     * Enable query result caching
     * 
     * @param int $ttl Time to live in seconds
     * @return self
     */
    public function cache(int $ttl = 3600): self
    {
        $this->cacheEnabled = true;
        $this->cacheTtl = $ttl;
        return $this;
    }

    /**
     * Add WHERE condition
     */
    public function where(string $column, mixed $value, string $operator = '='): self
    {
        $placeholder = 'where_' . count($this->bindings);
        $this->wheres[] = "{$column} {$operator} :{$placeholder}";
        $this->bindings[$placeholder] = $value;
        return $this;
    }

    /**
     * Add AND WHERE condition
     */
    public function andWhere(string $column, mixed $value, string $operator = '='): self
    {
        return $this->where($column, $value, $operator);
    }

    /**
     * Add OR WHERE condition
     */
    public function orWhere(string $column, mixed $value, string $operator = '='): self
    {
        $placeholder = 'where_' . count($this->bindings);
        $this->wheres[] = "OR {$column} {$operator} :{$placeholder}";
        $this->bindings[$placeholder] = $value;
        return $this;
    }

    /**
     * Add WHERE BETWEEN condition
     */
    public function whereBetween(string $column, mixed $start, mixed $end): self
    {
        $startPlaceholder = 'start_' . count($this->bindings);
        $endPlaceholder = 'end_' . count($this->bindings);
        $this->wheres[] = "{$column} BETWEEN :{$startPlaceholder} AND :{$endPlaceholder}";
        $this->bindings[$startPlaceholder] = $start;
        $this->bindings[$endPlaceholder] = $end;
        return $this;
    }

    /**
     * Add ORDER BY clause
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = "ORDER BY {$column} {$direction}";
        return $this;
    }

    /**
     * Add GROUP BY clause
     */
    public function groupBy(string $column): self
    {
        $this->groupBy = "GROUP BY {$column}";
        return $this;
    }

    /**
     * Add LIMIT clause
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Add OFFSET clause
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Build the SQL query
     */
    private function buildSql(): string
    {
        $sql = "SELECT " . implode(', ', $this->columns) . " FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        if ($this->groupBy) {
            $sql .= " {$this->groupBy}";
        }

        if ($this->orderBy) {
            $sql .= " {$this->orderBy}";
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    /**
     * Execute and get all results
     */
    public function get(): array
    {
        // If caching is enabled, try to get from cache first
        if ($this->cacheEnabled) {
            $cacheKey = $this->getCacheKey();

            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        // Execute query
        $sql = $this->buildSql();
        $stmt = Model::conn()->prepare($sql);
        $stmt->execute($this->bindings);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Store in cache if caching is enabled
        if ($this->cacheEnabled) {
            Cache::tags([$this->table])->put($cacheKey, $results, $this->cacheTtl);
        }

        return $results;
    }

    /**
     * Generate cache key for query
     */
    private function getCacheKey(): string
    {
        $parts = [
            'query',
            $this->table,
            implode(',', $this->columns),
            implode('|', $this->wheres),
            serialize($this->bindings),
            $this->orderBy ?? '',
            $this->groupBy ?? '',
            $this->limit ?? '',
            $this->offset ?? ''
        ];

        return 'query:' . md5(implode(':', $parts));
    }

    /**
     * Execute and get first result
     */
    public function first(): ?array
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * Count results
     */
    public function count(): int
    {
        $this->columns = ['COUNT(*) as total'];
        $result = $this->first();
        return (int)($result['total'] ?? 0);
    }
}
