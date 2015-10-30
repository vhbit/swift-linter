# swift-linter

## Installation

What you want to get is to make this module to be available for
`arcanist`. There are a couple of ways to achieve it depending on your
requirements.

### Prerequisites

Right now `swiftlint` should be installed beforehand. On OS X you
can do it through [homebrew](https://brew.sh) `brew install
swiftlint`.

You also have to configure your rules in `.swiftlint.yml`
([documentation](https://github.com/realm/SwiftLint#configuration))

### Project-specific installation

You can add this repository as a git submodule and in this case
`.arcconfig` should look like:

```json
{
  "load": "path/to/submodule"
  // ...
}
```

### Global installation
`arcanist` can load modules from an absolute path. But there is one
more trick - it also searches for modules in a directory up one level
from itself.

It means that you can clone this repository to the same directory
where `arcanist` and `libphutil` are located. In the end it should
look like this:

```sh
> ls
arcanist
swift-linter
libphutil
```

In this case you `.arcconfig` should look like

```json
{
  "load": "swift-linter"
  // ...
}
```

Another approach is to clone `swift-linter` to a fixed location
and use absolute path like:

```sh
cd ~/.dev-tools
git clone https://github.com/vhbit/swift-linter
```

```json
{
  "load": "~/.dev-tools/swift-linter"
  // ...
}
```

Both ways of global installation are actually almost equally as in
most cases you'd like to have a bootstrapping script for all tools.

## Setup

Once installed, linter can be used and configured just as any other
`arcanist linter`

Here is a simplest `.arclint`:

```json
{
    "linters": {
        "swift": {
            "type": "swift-lint",
            "include": "(^Source/.*\\.swift$)"
        },
        // ...
    }
}
```
