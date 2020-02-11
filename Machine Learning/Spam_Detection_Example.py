import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.tree import DecisionTreeClassifier, export_graphviz
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import roc_auc_score
import matplotlib.pyplot as plt

data = pd.read_csv("spambase.csv", header=None)
data.fillna(0)
print("There are {0} instances and {1} features in the dataset.".format(data.shape[0], data.shape[1]))
print("There are {0} regular emails in the dataset.".format(len(data.loc[data[57] == 0])))
print("There are {0} spam emails in the dataset.".format(len(data.loc[data[57] == 1])))

X_train, X_test, y_train, y_test = train_test_split(data.iloc[:, 0:57], data[57], test_size=0.2, random_state=6740)

tree = DecisionTreeClassifier(max_depth=20)
tree.fit(X_train, y_train)
score = tree.score(X_test, y_test)
print("Test accuracy of decision tree:", round(score, 4))
# export_graphviz(tree, out_file="tree.dot")
y_pred_tree = tree.predict_proba(X_test)[:, 1]
print("Decision tree AUC:", round(roc_auc_score(y_test, y_pred_tree), 4))

forest = RandomForestClassifier(n_estimators=100, max_depth=20)
forest.fit(X_train, y_train)
score_forest = forest.score(X_test, y_test)
print("Test accuracy of random forest:", round(score_forest, 4))
y_pred_rf = forest.predict_proba(X_test)[:, 1]
print("Random forest AUC:", round(roc_auc_score(y_test, y_pred_rf), 4))

treeSize = range(2, 25)
scores = []
for i in treeSize:
    tree = DecisionTreeClassifier(max_depth=i)
    tree.fit(X_train, y_train)
    y_pred_tree = tree.predict_proba(X_test)[:, 1]
    auc = roc_auc_score(y_test, y_pred_tree)
    scores.append(auc)

plt.style.use('seaborn-whitegrid')
plt.plot(treeSize, scores)
plt.xlabel("Tree Size")
plt.ylabel("AUC")
plt.show()
