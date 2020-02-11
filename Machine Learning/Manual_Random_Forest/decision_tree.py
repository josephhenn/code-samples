from util import entropy, information_gain, partition_classes
import numpy as np
import ast

class DecisionTree(object):
    def __init__(self):
        # Initializing the tree as an empty dictionary or list, as preferred
        self.tree = []
        self.node_count = 0
        #self.tree = {}
        pass

    def learn(self, X, y):
        # TODO: Train the decision tree (self.tree) using the the sample X and labels y
        # You will have to make use of the functions in utils.py to train the tree
        
        # One possible way of implementing the tree:
        #    Each node in self.tree could be in the form of a dictionary:
        #       https://docs.python.org/2/library/stdtypes.html#mapping-types-dict
        #    For example, a non-leaf node with two children can have a 'left' key and  a 
        #    'right' key. You can add more keys which might help in classification
        #    (eg. split attribute and split value)
        X = np.array(X)
        max_ig = 0
        best_split_att = 0
        best_split_val = ''
        tree = []
        for a in range(np.shape(X)[1]):
            if len(X) > 20:
                rand = np.random.choice(len(X), size=round(len(X)/10), replace=False)
            else:
                rand = range(len(X))
            for n in rand:
                X_l, X_r, y_l, y_r = partition_classes(X, y, a, X[n, a])
                current_ig = information_gain(y, [y_l, y_r])
                if current_ig > max_ig:
                    max_ig = current_ig
                    best_split_att = a
                    best_split_val = X[n, a]
        X_left, X_right, y_left, y_right = partition_classes(X, y, best_split_att, best_split_val)
        if self.node_count == 0:
            self.tree.append([self.node_count, best_split_att, best_split_val, [], []])
            self.node_count += 1
            if (max(np.unique(X_left, axis=0, return_counts=True)[1]) == len(X_left)) | \
                    (max(np.bincount(y_left)) == len(y_left)) | (len(y_left) == 1):
                self.tree[0][3] = [self.node_count, -1, np.argmax(np.bincount(y_left))]
                self.node_count += 1
            else:
                self.tree[0][3] = self.learn(X_left, y_left)
            if (max(np.unique(X_right, axis=0, return_counts=True)[1]) == len(X_right)) | \
                    (max(np.bincount(y_right)) == len(y_right)) | (len(y_right) == 1):
                self.tree[0][4] = [self.node_count, -1, np.argmax(np.bincount(y_right))]
                self.node_count += 1
            else:
                self.tree[0][4] = self.learn(X_right, y_right)
            return self.tree
        else:
            tree.append([self.node_count, best_split_att, best_split_val, [], []])
            self.node_count += 1
            if (max(np.unique(X_left, axis=0, return_counts=True)[1]) == len(X_left)) | \
                    (max(np.bincount(y_left)) == len(y_left)) | (len(y_left) == 1):
                tree[0][3] = [self.node_count, -1, np.argmax(np.bincount(y_left))]
                self.node_count += 1
            else:
                tree[0][3] = self.learn(X_left, y_left)
            if (max(np.unique(X_right, axis=0, return_counts=True)[1]) == len(X_right)) | \
                    (max(np.bincount(y_right)) == len(y_right)) | (len(y_right) == 1):
                tree[0][4] = [self.node_count, -1, np.argmax(np.bincount(y_right))]
                self.node_count += 1
            else:
                tree[0][4] = self.learn(X_right, y_right)
            return tree

    def classify(self, record):
        # TODO: classify the record using self.tree and return the predicted label
        node = self.tree[0]
        if str(node[2]).isnumeric() is True:
            if record[node[1]] <= float(node[2]):
                node = node[3]
            else:
                node = node[4]
        else:
            if record[node[1]] == node[2]:
                node = node[3]
            else:
                node = node[4]
        while True:
            if len(node) == 3:
                label = node[2]
                break
            elif str(node[0][2]).isnumeric() is True:
                if record[node[0][1]] <= float(node[0][2]):
                    node = node[0][3]
                else:
                    node = node[0][4]
            else:
                if record[node[0][1]] == node[0][2]:
                    node = node[0][3]
                else:
                    node = node[0][4]
        return label

