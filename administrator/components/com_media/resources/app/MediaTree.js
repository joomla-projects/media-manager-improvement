/**
 * A media node
 */
class MediaNode {
    constructor(path, type, parent = null, children = []) {
        this.path = path;
        this.type = type;
        this.parent = parent;
        this.children = children;
    }
}

/**
 * The media tree
 */
export class MediaTree {
    constructor(path) {
        this._root = new MediaNode(path, 'dir');
    }

    add(path, parentPath) {
        const child = new Node(path);
        // Find the parent node
        const parent = this._findByPath(parentPath);
        if (parent) {
            parent.children.push(child);
            child.parent = parent;
        } else {
            throw new Error('[Media Tree] Parent node ' + parentPath + ' does not exist.');
        }
    }

    remove(path, parentPath) {
        const tree = this;
        let childToRemove = null,
            index;
        // Find the parent node
        const parent = this._findByPath(parentPath);
        if (parent) {
            index = this._indexOfPath(parent.children, path);
            if (index === undefined) {
                throw new Error('[Media Tree] Node ' + path + ' does not exist.');
            } else {
                childToRemove = parent.children.splice(index, 1);
            }
        } else {
            throw new Error('[Media Tree] Parent node ' + parentPath + ' does not exist.');
        }

        return childToRemove;
    }

    _traverse(callbackFn) {
        (function recurse(currentNode) {
            const length = currentNode.children.length;
            for (let i = 0; i < length; i++) {
                recurse(currentNode.children[i]);
            }
            callbackFn(currentNode);
        })(this._root);
    }

    _findByPath(path) {
        let found = null;
        this._traverse((node) => {
            if (node.path === path) {
                found = node;
            }
        });
        return found;
    }

    _indexOfPath(array, path) {
        const length = array.length;
        let index;
        for (var i = 0; i < length; i++) {
            if (array[i].path === path) {
                index = i;
                break;
            }
        }

        return index;
    }
}
