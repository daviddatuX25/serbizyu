#!/usr/bin/env python3
"""
Code Map Generator - Creates a text file map of your codebase
Usage: 
  python code_map.py <folder_path> [options]
  python code_map.py --config config.json
"""

import os
import sys
import json
import argparse
from pathlib import Path
from datetime import datetime

# Default text file extensions
DEFAULT_TEXT_EXTENSIONS = {
    '.php', '.py', '.js', '.jsx', '.ts', '.tsx', '.html', '.css', '.scss',
    '.json', '.xml', '.yml', '.yaml', '.md', '.txt', '.sql', '.sh', '.bash',
    '.java', '.c', '.cpp', '.h', '.hpp', '.cs', '.go', '.rs', '.rb', '.vue',
    '.svelte', '.astro', '.env', '.gitignore', '.htaccess', '.conf', '.ini',
    '.sass', '.less', '.lock', '.toml', '.dockerfile'
}

# Default skip patterns
DEFAULT_SKIP_PATTERNS = {
    'node_modules', '.git', '.svn', '__pycache__', 'vendor', 
    '.idea', '.vscode', 'dist', 'build', '.next', '.cache',
    'coverage', '.pytest_cache', '.mypy_cache', 'venv', 'env'
}

class CodeMapGenerator:
    def __init__(self, config=None):
        self.config = config or {}
        self.text_extensions = set(self.config.get('text_extensions', DEFAULT_TEXT_EXTENSIONS))
        self.skip_patterns = set(self.config.get('exclude_folders', DEFAULT_SKIP_PATTERNS))
        self.include_folders_only = self.config.get('include_folders_only', [])
        self.add_include_folders = self.config.get('add_include_folders', [])
        self.exclude_files = set(self.config.get('exclude_files', []))
        self.include_files_only = set(self.config.get('include_files_only', []))
        self.add_include_files = set(self.config.get('add_include_files', []))
        self.max_file_size = self.config.get('max_file_size_mb', 10) * 1024 * 1024
        self.stats = {
            'text_files': 0,
            'binary_files': 0,
            'skipped_files': 0,
            'errors': 0,
            'total_size': 0
        }
    
    def should_skip_folder(self, folder_path, base_path):
        """Check if folder should be skipped"""
        rel_path = Path(folder_path).relative_to(base_path)
        parts = rel_path.parts
        rel_str = str(rel_path).replace('\\', '/')
        
        # Check against exclude patterns
        if any(skip in parts for skip in self.skip_patterns):
            return True
        
        # If include_folders_only is specified, only process those (whitelist mode)
        if self.include_folders_only:
            return not any(
                rel_str.startswith(inc) or rel_str == inc 
                for inc in self.include_folders_only
            )
        
        # If add_include_folders is specified, these are ALWAYS included (even if normally excluded)
        if self.add_include_folders:
            if any(rel_str.startswith(inc) or rel_str == inc for inc in self.add_include_folders):
                return False
        
        return False
    
    def should_skip_file(self, file_path, base_path):
        """Check if file should be skipped"""
        rel_path = Path(file_path).relative_to(base_path)
        rel_str = str(rel_path).replace('\\', '/')
        filename = file_path.name
        
        # add_include_files: ALWAYS include these files (highest priority)
        if filename in self.add_include_files or rel_str in self.add_include_files:
            return False
        
        # Check explicit exclusions
        if filename in self.exclude_files or rel_str in self.exclude_files:
            return True
        
        # include_files_only: ONLY include these files (whitelist mode)
        if self.include_files_only:
            return not (filename in self.include_files_only or rel_str in self.include_files_only)
        
        # Check file size
        try:
            if file_path.stat().st_size > self.max_file_size:
                return True
        except:
            pass
        
        return False
    
    def is_text_file(self, file_path):
        """Check if file should have its content included"""
        ext = Path(file_path).suffix.lower()
        return ext in self.text_extensions
    
    def read_file_safely(self, file_path):
        """Try to read file content, return error message if fails"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                return f.read()
        except UnicodeDecodeError:
            try:
                with open(file_path, 'r', encoding='latin-1') as f:
                    return f.read()
            except Exception as e:
                self.stats['errors'] += 1
                return f"[Error reading file: {str(e)}]"
        except Exception as e:
            self.stats['errors'] += 1
            return f"[Error reading file: {str(e)}]"
    
    def generate_map(self, folder_path, output_file):
        """Generate the code map"""
        folder_path = Path(folder_path).resolve()
        
        if not folder_path.exists():
            print(f"Error: Folder '{folder_path}' does not exist!")
            return False
        
        if not folder_path.is_dir():
            print(f"Error: '{folder_path}' is not a directory!")
            return False
        
        # Create output directory if needed
        output_path = Path(output_file)
        output_path.parent.mkdir(parents=True, exist_ok=True)
        
        print(f"Scanning folder: {folder_path}")
        print(f"Output file: {output_file}")
        print("-" * 70)
        
        with open(output_file, 'w', encoding='utf-8') as out:
            # Minimal header
            out.write(f"MAP: {folder_path}\n")
            
            # Walk through all files
            for root, dirs, files in os.walk(folder_path):
                # Filter directories
                dirs[:] = [d for d in dirs 
                          if not self.should_skip_folder(Path(root) / d, folder_path)]
                
                if self.should_skip_folder(root, folder_path):
                    continue
                
                # Process files
                for file in sorted(files):
                    file_path = Path(root) / file
                    rel_path = file_path.relative_to(folder_path)
                    display_path = str(rel_path).replace('\\', '/')
                    
                    if self.should_skip_file(file_path, folder_path):
                        self.stats['skipped_files'] += 1
                        print(f"⊘ Skipped: {display_path}")
                        continue
                    
                    # Get file size
                    try:
                        file_size = file_path.stat().st_size
                        self.stats['total_size'] += file_size
                    except:
                        file_size = 0
                    
                    if self.is_text_file(file_path):
                        # Text file - include content (optimized format)
                        out.write(f"--{display_path}\n")
                        content = self.read_file_safely(file_path)
                        out.write(content)
                        if not content.endswith('\n'):
                            out.write('\n')
                        out.write("----\n")
                        self.stats['text_files'] += 1
                        print(f"✓ Processed: {display_path}")
                    else:
                        # Binary file - register name only (minimal format)
                        out.write(f"--{display_path}\n")
                        self.stats['binary_files'] += 1
                        print(f"◆ Registered: {display_path}")
            
            # Minimal summary
            out.write(f"--SUMMARY\nText:{self.stats['text_files']} Binary:{self.stats['binary_files']} Skipped:{self.stats['skipped_files']}\n")
        
        print("-" * 70)
        print(f"✓ Code map generated successfully!")
        print(f"  Text files: {self.stats['text_files']}")
        print(f"  Binary files: {self.stats['binary_files']}")
        print(f"  Skipped: {self.stats['skipped_files']}")
        print(f"  Total size: {self.stats['total_size'] / (1024*1024):.2f} MB")
        print(f"  Output: {output_file}")
        return True

def load_config(config_path):
    """Load configuration from JSON file"""
    try:
        with open(config_path, 'r') as f:
            return json.load(f)
    except Exception as e:
        print(f"Error loading config file: {e}")
        sys.exit(1)

def create_sample_config(output_path='code_map_config.json'):
    """Create a sample configuration file"""
    sample_config = {
        "source_folders": [
            "./app",
            "./src"
        ],
        "output_folder": "./output",
        "output_filename": "code_map.txt",
        "include_folders_only": [],
        "add_include_folders": [
            "app/special",
            "src/important"
        ],
        "exclude_folders": [
            "node_modules",
            ".git",
            "vendor",
            "dist",
            "build"
        ],
        "include_files_only": [],
        "add_include_files": [
            "config.php",
            "app/critical.js"
        ],
        "exclude_files": [
            "package-lock.json",
            "composer.lock"
        ],
        "text_extensions": [
            ".php", ".js", ".py", ".html", ".css",
            ".json", ".md", ".txt", ".yml", ".env"
        ],
        "max_file_size_mb": 10,
        "_comments": {
            "include_folders_only": "WHITELIST MODE: Only process these folders (if set, ignores all others)",
            "add_include_folders": "ALWAYS include these folders (even if they match exclude patterns)",
            "include_files_only": "WHITELIST MODE: Only process these files (if set, ignores all others)",
            "add_include_files": "ALWAYS include these files (even if excluded by other rules)"
        }
    }
    
    with open(output_path, 'w') as f:
        json.dump(sample_config, f, indent=2)
    
    print(f"Sample configuration created: {output_path}")
    print("\nConfiguration modes:")
    print("  include_folders_only: Whitelist mode - ONLY these folders")
    print("  add_include_folders: Add specific folders to normal scan")
    print("  include_files_only: Whitelist mode - ONLY these files")
    print("  add_include_files: Add specific files to normal scan")
    print("\nEdit this file and run: python code_map.py --config code_map_config.json")

def main():
    parser = argparse.ArgumentParser(
        description='Generate a comprehensive text map of your codebase',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python code_map.py ./my-project
  python code_map.py ./my-project -o ./output/map.txt
  python code_map.py --config config.json
  python code_map.py --create-config
        """
    )
    
    parser.add_argument('folder', nargs='?', help='Folder to scan')
    parser.add_argument('-o', '--output', help='Output file path (default: code_map.txt)')
    parser.add_argument('--output-folder', help='Output folder for generated map')
    parser.add_argument('--config', help='Path to JSON configuration file')
    parser.add_argument('--create-config', action='store_true', 
                       help='Create a sample configuration file')
    parser.add_argument('--exclude', nargs='+', help='Additional folders to exclude')
    parser.add_argument('--include-only', nargs='+', help='ONLY include these folders (whitelist)')
    parser.add_argument('--add-include', nargs='+', help='Add these folders/files to include')
    parser.add_argument('--add-files', nargs='+', help='Add specific files to include')
    
    args = parser.parse_args()
    
    # Create sample config
    if args.create_config:
        create_sample_config()
        sys.exit(0)
    
    # Load config or create default
    if args.config:
        config = load_config(args.config)
        
        # Process multiple source folders if specified in config
        source_folders = config.get('source_folders', [])
        if not source_folders:
            print("Error: No source_folders specified in config file")
            sys.exit(1)
        
        output_folder = config.get('output_folder', '.')
        output_filename = config.get('output_filename', 'code_map.txt')
        output_path = Path(output_folder) / output_filename
        
        generator = CodeMapGenerator(config)
        
        # Process all source folders into one map
        success = True
        for folder in source_folders:
            if Path(folder).exists():
                print(f"\n{'=' * 70}")
                print(f"Processing: {folder}")
                print(f"{'=' * 70}")
                success = generator.generate_map(folder, output_path) and success
            else:
                print(f"Warning: Folder not found: {folder}")
        
        sys.exit(0 if success else 1)
    
    # Manual mode
    if not args.folder:
        parser.print_help()
        sys.exit(1)
    
    # Build config from arguments
    config = {}
    if args.exclude:
        config['exclude_folders'] = DEFAULT_SKIP_PATTERNS | set(args.exclude)
    if args.include_only:
        config['include_folders_only'] = args.include_only
    if args.add_include:
        config['add_include_folders'] = args.add_include
    if args.add_files:
        config['add_include_files'] = args.add_files
    
    # Determine output path
    if args.output:
        output_file = args.output
    elif args.output_folder:
        output_file = Path(args.output_folder) / 'code_map.txt'
    else:
        output_file = 'code_map.txt'
    
    generator = CodeMapGenerator(config)
    success = generator.generate_map(args.folder, output_file)
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()