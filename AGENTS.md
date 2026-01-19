# Codebase Agent Guidelines

## Build/Lint/Test Commands

### Build Commands

- `npm run build` - Build the application for production
- `npm run dev` - Start development server (port 5174)
- `npm run type-check` - Run TypeScript type checking
- `npm run type-check:watch` - Run TypeScript type checking in watch mode

### Linting

- `npm run lint` - Run ESLint (no specific lint command defined, but using ESLint config)
- `npm run format` - Format code using Prettier (not explicitly defined in package.json but using prettier-plugin)

### Testing

- No explicit test commands found in package.json
- Tests should be run with their respective framework commands (e.g., vitest, jest)

## Code Style Guidelines

### Imports

- Use ES module syntax with explicit file extensions
- Import paths use `@/` prefix for app root (configured in tsconfig.json)
- Organize imports with prettier-plugin-organize-imports
- Group imports: external libraries, internal modules, relative paths

### Formatting

- Use Prettier for code formatting
- Follow ESLint + Prettier configuration
- JSX/TSX files formatted with automatic JSX transform
- Line endings: Unix-style (LF)
- Indentation: 2 spaces
- Max line width: 80-120 characters

### Types

- TypeScript is enabled with strict checking
- Interface naming follows PascalCase
- Type naming uses PascalCase
- Generic types named with single uppercase letter (T, U, V)
- Use readonly for immutable data structures
- Prefer readonly arrays over mutable arrays when possible

### Naming Conventions

- Variables and functions: camelCase
- Components: PascalCase
- Constants: UPPER_CASE
- Class names: PascalCase
- File names: PascalCase for components, camelCase for utilities

### Error Handling

- Use React Error Boundary for unhandled errors
- Leverage react-error-boundary for error wrapping
- Implement appropriate error catching patterns
- Handle asynchronous errors with try/catch blocks
- Use proper error messages for user feedback

### React Components

- Use functional components with hooks
- Follow React 17+ JSX runtime (no explicit React import needed)
- Use TypeScript interfaces for component props
- Implement proper component structure with clear separation of concerns

### Additional Configuration

- Uses Tailwind CSS with Tailwind CSS plugin
- Uses Vite as build tool
- Uses Inertia.js for React integration
- Uses Radix UI components for accessible UI elements
- Uses Tailwind merge for class composition
