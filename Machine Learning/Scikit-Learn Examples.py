## Data and Visual Analytics - Homework 4
## Georgia Institute of Technology
## Applying ML algorithms to detect eye state

import numpy as np
import pandas as pd
import time

from sklearn.model_selection import cross_val_score, GridSearchCV, cross_validate, train_test_split
from sklearn.metrics import accuracy_score, classification_report
from sklearn.svm import SVC
from sklearn.linear_model import LinearRegression
from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import StandardScaler, normalize
from sklearn.decomposition import PCA

######################################### Reading and Splitting the Data ###############################################
# XXX
# TODO: Read in all the data. Replace the 'xxx' with the path to the data set.
# XXX
data = pd.read_csv('eeg_dataset.csv')

# Separate out the x_data and y_data.
x_data = data.loc[:, data.columns != "y"]
y_data = data.loc[:, "y"]

# The random state to use while splitting the data.
random_state = 100

# XXX
# TODO: Split 70% of the data into training and 30% into test sets. Call them x_train, x_test, y_train and y_test.
# Use the train_test_split method in sklearn with the parameter 'shuffle' set to true and the 'random_state' set to 100.
# XXX
x_train, x_test, y_train, y_test = train_test_split(x_data, y_data, test_size=0.3, train_size=0.7, shuffle=True, random_state=random_state)

# ############################################### Linear Regression ###################################################
# XXX
# TODO: Create a LinearRegression classifier and train it.
# XXX
lm = LinearRegression()
lm.fit(x_train, y_train)

# XXX
# TODO: Test its accuracy (on the training set) using the accuracy_score method.
# TODO: Test its accuracy (on the testing set) using the accuracy_score method.
# Note: Round the output values greater than or equal to 0.5 to 1 and those less than 0.5 to 0. You can use y_predict.round() or any other method.
# XXX
lm_pred1 = lm.predict(x_train)
for a in np.where(lm_pred1 >= 0.5):
    lm_pred1[a] = 1.0
for a in np.where(lm_pred1 < 0.5):
    lm_pred1[a] = 0.0
print(accuracy_score(y_train, lm_pred1))
lm_pred2 = lm.predict(x_test)
for a in np.where(lm_pred2 >= 0.5):
    lm_pred2[a] = 1.0
for a in np.where(lm_pred2 < 0.5):
    lm_pred2[a] = 0.0
print(accuracy_score(y_test, lm_pred2))

# ############################################### Random Forest Classifier ##############################################
# XXX
# TODO: Create a RandomForestClassifier and train it.
# XXX
rf = RandomForestClassifier(n_estimators=100, random_state=random_state)
rf.fit(x_train, y_train)

# XXX
# TODO: Test its accuracy on the training set using the accuracy_score method.
# TODO: Test its accuracy on the test set using the accuracy_score method.
# XXX
rf_pred1 = rf.predict(x_train)
rf_pred2 = rf.predict(x_test)
print(accuracy_score(y_train, rf_pred1))
print(accuracy_score(y_test, rf_pred2))

# XXX
# TODO: Determine the feature importance as evaluated by the Random Forest Classifier.
#       Sort them in the descending order and print the feature numbers. The report the most important and the least important feature.
#       Mention the features with the exact names, e.g. X11, X1, etc.
#       Hint: There is a direct function available in sklearn to achieve this. Also checkout argsort() function in Python.
# XXX
features = list(data.columns[0:14])
importances = rf.feature_importances_
for i in np.argsort(rf.feature_importances_)[::-1]:
    print(features[i], importances[i])

# XXX
# TODO: Tune the hyper-parameters 'n_estimators' and 'max_depth'.
#       Print the best params, using .best_params_, and print the best score, using .best_score_.
# Get the training and test set accuracy values after hyperparameter tuning.
# XXX
# rf2 = RandomForestClassifier(random_state=random_state)
# rf_parameters = {'n_estimators': [50, 100, 200], 'max_depth': [5, 10, 20]}
# rf_tune = GridSearchCV(rf2, rf_parameters, cv=10)
# rf_tune.fit(x_train, y_train)
# rf_tune_pred = rf_tune.predict(x_test)
# print(accuracy_score(y_test, rf_tune_pred))
# print(rf_tune.best_params_)
# print(rf_tune.best_score_)

# ############################################ Support Vector Machine ###################################################
# XXX
# TODO: Pre-process the data to standardize or normalize it, otherwise the grid search will take much longer
# TODO: Create a SVC classifier and train it.
# XXX
x_train_normal = normalize(x_train)
x_test_normal = normalize(x_test)

svc = SVC(gamma='auto')
svc.fit(x_train_normal, y_train)

# XXX
# TODO: Test its accuracy on the training set using the accuracy_score method.
# TODO: Test its accuracy on the test set using the accuracy_score method.
# XXX
svm_pred1 = svc.predict(x_train_normal)
svm_pred2 = svc.predict(x_test_normal)
print(accuracy_score(y_train, svm_pred1))
print(accuracy_score(y_test, svm_pred2))


# XXX
# TODO: Tune the hyper-parameters 'C' and 'kernel' (use rbf and linear).
#       Print the best params, using .best_params_, and print the best score, using .best_score_.
# Get the training and test set accuracy values after hyperparameter tuning.
# XXX
svm_parameters = {'kernel': ('linear', 'rbf'), 'C': [.01, .1, 1, 10, 100]}
svm_tune = GridSearchCV(svc, svm_parameters, cv=10)
svm_tune_fit = svm_tune.fit(x_train_normal, y_train)
svm_pred3 = svm_tune_fit.predict(x_test_normal)
print(accuracy_score(y_test, svm_pred3))
print(svm_tune.best_params_)
print(svm_tune.best_score_)

# XXX
# TODO: Calculate the mean training score, mean testing score and mean fit time for the 
# best combination of hyperparameter values that you obtained in Q3.2. The GridSearchCV 
# class holds a  ‘cv_results_’ dictionary that should help you report these metrics easily.
# XXX
print(np.mean(svm_tune.cv_results_['mean_train_score']))
print(np.mean(svm_tune.cv_results_['mean_test_score']))
print(np.mean(svm_tune.cv_results_['mean_fit_time']))

# ######################################### Principal Component Analysis #################################################
# XXX
# TODO: Perform dimensionality reduction of the data using PCA.
#       Set parameters n_component to 10 and svd_solver to 'full'. Keep other parameters at their default value.
#       Print the following arrays:
#       - Percentage of variance explained by each of the selected components
#       - The singular values corresponding to each of the selected components.
# XXX

pca = PCA(n_components=10, svd_solver='full')
pca.fit(x_data)
print(pca.explained_variance_ratio_)
print(pca.singular_values_)
