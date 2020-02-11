package edu.gatech.cse6242;

import java.util.StringTokenizer;
import java.io.IOException;
import org.apache.hadoop.fs.Path;
import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.io.*;
import org.apache.hadoop.mapreduce.*;
import org.apache.hadoop.util.*;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;

public class Q1 {

  public static class NodeMapper
	extends Mapper<Object, Text, IntWritable, IntWritable>{

    private IntWritable weight = new IntWritable();
    private IntWritable node = new IntWritable();
    private IntWritable src = new IntWritable();

    public void map(Object key, Text value, Context context) 
		throws IOException, InterruptedException{
	StringTokenizer nodeline = new StringTokenizer(value.toString());
	while (nodeline.hasMoreTokens()){
	   src.set(Integer.parseInt(nodeline.nextToken()));
	   node.set(Integer.parseInt(nodeline.nextToken()));
	   weight.set(Integer.parseInt(nodeline.nextToken()));
	   context.write(node, weight);
	}
    }
  }

  public static class NodeReducer
	extends Reducer<IntWritable, IntWritable, IntWritable, IntWritable> {

    private IntWritable result = new IntWritable();
    public void reduce(IntWritable key, Iterable<IntWritable> values, Context context)
		throws IOException, InterruptedException{
      int sum = 0;
      for (IntWritable val : values){
        sum+= val.get();
      }
      result.set(sum);
      if(sum > 0) {
        context.write(key, result);
      }
    }
  }

  public static void main(String[] args) throws Exception {
    Configuration conf = new Configuration();
    Job job = Job.getInstance(conf, "Q1");

    /* TODO: Needs to be implemented */
    job.setJarByClass(Q1.class);
    job.setMapperClass(NodeMapper.class);
    job.setCombinerClass(NodeReducer.class);
    job.setReducerClass(NodeReducer.class);
    job.setOutputKeyClass(IntWritable.class);
    job.setOutputValueClass(IntWritable.class);

    FileInputFormat.addInputPath(job, new Path(args[0]));
    FileOutputFormat.setOutputPath(job, new Path(args[1]));
    System.exit(job.waitForCompletion(true) ? 0 : 1);
  }
}